<?php

namespace app\controllers;

use app\components\helpers\CookieAuth;
use app\components\helpers\CookieConsent;
use Yii;
use app\components\EmailHelper;
use app\components\Header;
use app\components\ActiveForm;
use app\components\helpers\Html;
use app\components\helpers\Link;
use app\components\helpers\OpenSsl;
use app\models\db\User;
use app\models\db\UserAuth;
use app\models\forms\LoginForm;
use app\models\forms\NewPasswordForm;
use app\models\forms\PasswordRecoveryForm;
use app\models\forms\RegistrationForm;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use app\components\exceptions\AuthException;

/**
 * Authentication handler for the front end.
 *
 * @package app\controllers
 */
class AuthController extends Controller
{
    /**
     * @var string
     */
    const URL_REFERRER = 'urlReferrer';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'logout', 'report', 'login'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'login',
                            'register',
                            'auth',
                            'password-recovery',
                            'set-new-password',
                            'confirm-registration',
                            'confirm-token-reset',
                            'remember-me-handler',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['error'],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    /**
     * This function will be triggered when user is successfully authenticated using some oAuth client.
     *
     * @param \yii\authclient\ClientInterface $client
     * @return boolean|\yii\web\Response|void
     */
    public function oAuthSuccess($client)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $clientId = $client->getId();
            $attributes = $client->getUserAttributes();

            if ($clientId == 'facebook') {
                $id = $attributes['id'];
                $email = $attributes['email'];
                $firstName = ArrayHelper::getValue($attributes, 'first_name', 'myProject');
                $lastName = ArrayHelper::getValue($attributes, 'last_name', 'myProject');
                $image_url = $attributes['picture']['data']['url'];
            } elseif ($clientId == 'google') {
                $id = $attributes['id'];
                $email = $attributes['emails'][0]['value'];
                $firstName = $attributes['name']['givenName'];
                $lastName = $attributes['name']['familyName'];
                $image_url = $attributes['image']['url'];

                if (isset($image_url) && strpos($image_url, 'sz=50') !== false) {
                    // get a bigger profile picture instead of the default 50x50
                    $image_url = str_replace('sz=50', 'sz=200', $image_url);
                }
            } else {
                return;
            }

            /* @var \app\models\db\User $user */
            $user = User::find()->joinWith('userAuths')->where(['user_auth.source' => $clientId, 'user_auth.source_id' => $id])->one();

            if (self::logInUser($user)) {
                $user->updatePictureUrl($image_url);
                $transaction->commit();
                return $this->redirectBack();
            }

            $user = User::find()->where(['email' => $email])->one();

            if (self::logInUser($user)) {
                $auth = UserAuth::findOne(['user_id' => $user->id]);
                $message = null;

                if (empty($auth)) {
                    $auth = new UserAuth();
                    $message = Yii::t('auth', 'social-profile-joined-to-account', [
                        'socialProvider' => ucwords($clientId),
                    ]);
                }

                $auth->user_id = $user->id;
                $auth->source = $clientId;
                $auth->source_id = $id;

                if (!$auth->save()) {
                    throw new Exception(Html::errorSummary($auth));
                }

                $user->updatePictureUrl($image_url);
                $transaction->commit();

                if ($message !== null) {
                    Yii::$app->session->setFlash('info', $message);
                }

                return $this->redirectBack();
            }

            $user = User::factory([
                'email' => $email,
                'last_name' => $lastName,
                'first_name' => $firstName,
            ]);

            if (!$user->save()) {
                throw new Exception(Html::errorSummary($user));
            }

            $user->updatePictureUrl($image_url);

            $auth = new UserAuth();
            $auth->source = $clientId;
            $auth->source_id = $id;
            $auth->user_id = $user->id;
            $auth->save();

            if (!$auth->save()) {
                throw new Exception(Html::errorSummary($auth));
            }

            self::logInUser($user);
            $transaction->commit();

            return $this->redirectBack();
        } catch (Exception $e) {
            try {
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger', Html::encode($e->getMessage()));
                Yii::$app->controller->redirect(Link::to(Link::AUTH_LOGIN));
                Yii::$app->end();
            } catch (\Exception $e) {
                // apparently do nothing
            }
        }
    }

    /**
     * Helper function to log in the User.
     *
     * @param \app\models\db\User $user The User instance
     * @return bool True, if the User can be logged in
     */
    private static function logInUser($user)
    {
        if ($user === null) {
            return false;
        }

        if (Yii::$app->user->login($user)) {
            $user->generateAuthKey(
                Yii::$app->session->get(CookieAuth::REMEMBER_ME)
                    ? Yii::$app->params['publicAuthKeyExpiration']
                    : 0
            );
            $user->last_login_at = time();
            $user->last_login_ip = Yii::$app->request->userIP;
            $user->status = User::STATUS_ACTIVE;
            $user->save();
            return true;
        }

        return false;
    }

    /**
     * This hook redirects to referrer url if session urlReferrer exists
     * (cannot redirect to /bejelentkezes nor /regisztracio)
     *
     * It has a fallback value to redirect back to main website
     *
     * @return Response
     */
    private function redirectBack()
    {
        if ($referrerSession = Yii::$app->session->get(self::URL_REFERRER)) {
            Yii::$app->session->remove(self::URL_REFERRER);
            return $this->redirect($referrerSession);
        }

        return $this->goHome();
    }

    /**
     * Handles the ordinary email address and password login.
     *
     * Setting up a session with key urlReferrer to handle proper redirects
     *
     * @param int $fromNewReport
     * @return string|\yii\web\Response
     */
    public function actionLogin($fromNewReport = 0)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!$referrerSession = Yii::$app->session->get(self::URL_REFERRER)) {
            $urlReferrer = Yii::$app->request->getReferrer();

            // bejelentkezes or regisztracio shouldn't appear in any circumstances..
            if (
                'bejelentkezes' != $urlReferrer ||
                'regisztracio' != $urlReferrer
            ) {
                Yii::$app->session->set(
                    'urlReferrer',
                    $urlReferrer
                );
            }
        }

        $model = new LoginForm();

        if ($fromNewReport) {
            Yii::$app->user->setReturnUrl(Link::to(Link::CREATE_REPORT));
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->login()) {
            if ($model->getUser()->is_old_password) {
                return $this->redirect(Link::to(Link::PROFILE_MANAGE, ['#' => 'change-password']));
            }

            return $this->redirectBack();
        }

        Header::registerTag(Header::TYPE_TITLE, Yii::t('auth', 'login-legend'));

        return $this->render('login', [
            'model' => $model,
            'fromNewReport' => $fromNewReport,
        ]);
    }

    /**
     * Registers a User with email address and password.
     * User confirmation is the piece of the flow since 2018-12-16
     *
     * @return string|array|\yii\web\Response
     * @throws \Exception
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Header::registerTag(Header::TYPE_TITLE, Yii::t('auth', 'register-legend'));

        $model = new RegistrationForm();
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }

            if (!$model->save()) {
                Yii::error('Unable to create User! Errors: ' . print_r($model->getErrors(), true));
            }

            Yii::$app->session->setFlash('success', Yii::t('auth', 'successful-registration'));
            return $this->redirect(Link::to(Link::HOME));
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Handling registration confirmation
     */
    public function actionConfirmRegistration()
    {
        try {
            // using getQueryString because getQueryParam encodes it and breaks token
            if (!$token = Yii::$app->getRequest()->getQueryString()) {
                throw new AuthException(Yii::t('auth', 'confirmation-something-went-wrong'));
            }

            $tokenSplit = preg_split(
                '/-/',
                OpenSsl::decrypt(preg_split('/token=/', $token)[1])
            );

            // storing recycled values of token in variables
            if (!($time = $tokenSplit[0]) || !($userId = $tokenSplit[1])) {
                Yii::$app->session->setFlash(
                    'danger',
                    Yii::t('auth', 'confirmation-something-went-wrong')
                );
                // invalid data redirect to home
                return $this->goHome();
            }

            if (!$user = User::findOne(['id' => $userId])) {
                throw new AuthException(Yii::t('auth', 'confirmation-something-went-wrong'));
            }

            if ($user->status != User::STATUS_UNCONFIRMED) {
                throw new AuthException(Yii::t('auth', 'confirmation-something-went-wrong'));
            }

            // check if token has been expired
            if ($time < time()) {
                throw new AuthException(
                    Yii::t(
                        'auth',
                        'token-has-been-expired',
                        [
                            'link' => Link::to(
                                sprintf(
                                    '/auth/confirm-token-reset?data=%s',
                                    OpenSsl::encrypt($userId)
                                )
                            ),
                        ]
                    )
                );
            }

            // setting user status to active
            if (!$user->activateRegistration()) {
                throw new AuthException(Yii::t('auth', 'confirmation-something-went-wrong'));
            }

            if (!self::logInUser($user)) {
                throw new AuthException(Yii::t('auth', 'confirmation-something-went-wrong'));
            }

            Yii::$app->session->setFlash('success', Yii::t('auth', 'confirmation-successful'));
            EmailHelper::sendUserRegisteredViaRegForm($user);
        } catch (AuthException $e) {
            // catching exceptions and display them via flash message
            Yii::$app->session->setFlash('danger', $e->getMessage());

            if (isset($userId)) {
                return $this->render('password-token-reset', [
                    'userIdHash' => OpenSsl::encrypt($userId),
                ]);
            }
        }

        return $this->goHome();
    }

    /**
     * Action to handle confirmation token reset
     */
    public function actionConfirmTokenReset()
    {
        try {
            // using getQueryString because getQueryParam encodes it and breaks data
            if (!$data = Yii::$app->getRequest()->getQueryString()) {
                throw new AuthException(Yii::t('auth', 'reset-token-something-went-wrong'));
            }

            if (!$user = User::findOne([
                'id' => OpenSsl::decrypt(preg_split('/data=/', $data)[1]),
            ])) {
                throw new AuthException(Yii::t('auth', 'reset-token-something-went-wrong'));
            }

            // just in case of status is unconfirmed resending the confirmation email
            if ($user->status != User::STATUS_UNCONFIRMED) {
                throw new AuthException(Yii::t('auth', 'reset-token-something-went-wrong'));
            }

            Yii::$app->session->setFlash('success', Yii::t('auth', 'token-generated-successfully'));
            EmailHelper::sendUserRegistrationConfirm($user);
        } catch (AuthException $e) {
            // catching exceptions and display them via flash message
            Yii::$app->session->setFlash('danger', $e->getMessage());
        }

        return $this->goHome();
    }

    /**
     * Handles the logout.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionPasswordRecovery($success = 0)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('auth', 'password-recovery-legend'));

        $email = Yii::$app->request->get('email');
        $model = new PasswordRecoveryForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->processRecovery()) {
                return $this->redirect(Link::to(Link::AUTH_PASSWORD_RECOVERY, ['success' => 1]));
            }
        }

        return $this->render($success ? 'password-recovery-success' : 'password-recovery', [
            'model' => $model,
            'email' => $email,
        ]);
    }

    public function actionSetNewPassword($email, $token)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('auth', 'set-new-password.legend'));

        /** @var NewPasswordForm $model */
        $model = NewPasswordForm::findByEmail($email);

        if ($model === null) {
            Yii::$app->session->setFlash('danger', Yii::t('error', 'no-user-found'));
            return $this->redirect(Link::to(Link::AUTH_LOGIN));
        }

        if (!$model->validatePasswordRecoveryToken($token)) {
            Yii::$app->session->setFlash('danger', Yii::t('error', 'invalid-password-recovery-token'));
            return $this->redirect(Link::to(Link::AUTH_LOGIN));
        }

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }

            $model->setNewPassword();

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('auth', 'flash.new-password-successfully-set'));
                return $this->redirect(Link::to(Link::AUTH_LOGIN));
            }
        }

        return $this->render('set-new-password', [
            'newPasswordForm' => $model,
        ]);
    }

    /**
     *
     */
    public function actionRememberMeHandler()
    {
        if (Yii::$app->request->isAjax && CookieConsent::isAllowed()) {
            CookieAuth::prepareSession();
        }
    }
}
