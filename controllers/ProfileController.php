<?php

namespace app\controllers;

use app\components\helpers\Html;
use app\models\db\search\ReportSearchProfileView;
use app\models\forms\UserInfoChangeForm;
use Yii;
use app\components\Header;
use app\components\helpers\Link;
use app\models\db\search\ReportSearchProfile;
use app\models\db\Report;
use app\models\db\User;
use app\models\forms\PasswordChangeForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Handles Report related actions, like listing, creating a new Report and commenting on them.
 *
 * @package app\controllers
 */
class ProfileController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'au.upload' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new UserInfoChangeForm(), 'image_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/user',
                'saveCallback' => function ($path) {
                    /** @var User $user */
                    $user = Yii::$app->user->identity;
                    $user->image_file_name = $path;
                    return $user->storePicture()->save();
                },
            ],
            'au.thumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/user',
            ],
            'au.fullthumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/user',
                'useThumbs' => false,
            ],
            'au.delete' => [
                'class' => 'app\components\jqueryupload\DeleteAction',
                'uploadDest' => '@runtime/upload-tmp/user',
            ],
        ];
    }

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
                        'actions' => ['index', 'manage', 'au.upload', 'au.thumb', 'au.fullthumb', 'au.delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['switch-language'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'parse-video-url' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return Response
     */
    public function actionSwitchLanguage()
    {
        Yii::$app->session->set(
            'language',
            Yii::$app->request->get('language')
        );
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionIndex($status = null)
    {
        $user = Yii::$app->getUser()->getIdentity();

        if (isset($status) && $status === Report::STATUS_DRAFT) {
            Header::registerTag(Header::TYPE_TITLE, Yii::t('profile', 'title.drafts'));
        } else {
            Header::registerTag(Header::TYPE_TITLE, Yii::t('profile', 'title'));
        }

        $slugParams = ArrayHelper::merge([
            'ReportSearchProfile' => [
                'status' => $status,
            ],
        ], Yii::$app->request->get());

        $searchModel = new ReportSearchProfile(['user_id' => $user->id]);
        $dataProvider = $searchModel->search($slugParams);
        $dataProvider->pagination->pageSize = 8;
        $dataProvider->pagination->pageSizeParam = 'limit';

        return $this->render('profile', [
            'view' => false,
            'user' => $user,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $user = User::findOne(['id' => $id]);

        if (!$user) {
            return $this->redirect('/');
        }

        Header::registerTag(Header::TYPE_TITLE, Yii::t('profile', 'view_title', ['name' => $user->getFullName()]));

        $slugParams = ArrayHelper::merge([
            'ReportSearchProfileView' => [
                'status' => null,
            ],
        ], Yii::$app->request->get());

        $searchModel = new ReportSearchProfileView(['user_id' => $user->id]);
        $dataProvider = $searchModel->search($slugParams);
        $dataProvider->pagination->pageSize = 8;
        $dataProvider->pagination->pageSizeParam = 'limit';

        return $this->render('profile', [
            'view' => true,
            'user' => $user,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionManage()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('profile', 'menu.manage'));

        /** @var \app\models\db\User $user */
        $user = Yii::$app->getUser()->getIdentity();
        $notifications = User::notifications();
        $notificationTypes = User::notificationTypes();

        /** @var \App\models\db\User $currentUser */
        $currentUser = User::findOne(['id' => $user->id]);
        $userInfoForm = UserInfoChangeForm::findOne(['id' => $user->id]);

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app', 'successful_profile_update'));
            return $this->redirect(Link::to(Link::PROFILE_MANAGE));
        }

        if ($userInfoForm->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($userInfoForm);
            }

            if ($userInfoForm->save()) {
                Yii::$app->session->addFlash('success', Yii::t('app', 'successful_profile_update'));

                return $this->redirect(Link::to(Link::PROFILE_MANAGE));
            }
        }

        /** @var PasswordChangeForm $passwordForm */
        $passwordForm = PasswordChangeForm::findOne($user->id);

        if ($passwordForm->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($passwordForm);
            }

            if ($passwordForm->validate()) {
                $passwordForm->hashPassword($passwordForm->new_password);
                $passwordForm->is_old_password = 0;
                if ($passwordForm->save()) {
                    Yii::$app->session->addFlash('success', Yii::t('app', 'successful_password_update'));
                    return $this->redirect(Link::to(Link::PROFILE_MANAGE));
                }
            }
        }

        return $this->render('profile_manage', [
            'user' => $currentUser,
            'userInfoForm' => $userInfoForm,
            'notifications' => $notifications,
            'notificationTypes' => $notificationTypes,
            'passwordForm' => $passwordForm,
        ]);
    }
}
