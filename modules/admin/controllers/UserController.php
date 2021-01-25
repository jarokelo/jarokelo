<?php

namespace app\modules\admin\controllers;

use app\components\helpers\Key;
use app\models\db\Admin;
use app\models\db\User;
use app\models\forms\NewPasswordForm;
use app\modules\admin\models\UserSearch;
use Yii;

use yii\filters\AccessControl;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles User related actions.
 *
 * @package app\modules\admin\controllers
 */
class UserController extends Controller
{

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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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
            'au.upload' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new User(), 'image_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/user',
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
     * Display a list of Users.
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new UserSearch();

        $viewData = [
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'searchModel' => $searchModel,
        ];

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('index', $viewData);
        }

        return $this->render('index', $viewData);
    }

    /**
     * Handles the update of a User.
     *
     * @param integer $id The User's id
     * @return string|Response
     * @throws \Exception
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_EDIT)) {
            return $this->redirect(['index']);
        }

        /* @var \app\models\db\User $model */
        $model = $id === null ? null : User::find()->where(['id' => $id])->with('reports')->one();
        if ($model === null) {
            return $this->redirect(['index']);
        }

        $passwordForm = NewPasswordForm::findOne(['id' => $id]);

        if (Yii::$app->request->isPost && $passwordForm->load(Yii::$app->request->post())) {
            $passwordForm->hashPassword($passwordForm->new_password);
            if ($passwordForm->save()) {
                Yii::$app->session->addFlash('success', Yii::t('app', 'successful_password_update'));
                return $this->redirect(Url::to(['user/update', 'id' => $model->id]));
            }
        }

        $origFile = $model->image_file_name;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $newFile = $model->image_file_name;

            if (($newPicture = $model->isAttributeChanged('image_file_name')) && !empty($newFile)) {
                $parts = explode('.', $newFile);
                $ext = end($parts);

                $model->image_file_name = $model->id . '-' . uniqid() . '.' . $ext;
            }

            if ($model->save()) {
                if ($newPicture) {
                    if (!is_dir(Yii::getAlias('@app/web/files/user/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/user/thumb/'), 0777, true);
                    }

                    if (!empty($origFile)) {
                        @unlink(Yii::getAlias("@app/web/files/user/{$origFile}"));
                        @unlink(Yii::getAlias("@app/web/files/user/thumb/{$origFile}"));
                    }

                    if (!empty($newFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/user/{$newFile}"), Yii::getAlias("@app/web/files/user/{$model->image_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/user/thumb/{$newFile}"), Yii::getAlias("@app/web/files/user/thumb/{$model->image_file_name}"));
                    }
                }

                Yii::$app->session->addFlash('success', Yii::t('admin', 'update.success'));
                return $this->redirect(['index']);
            }

            Yii::error('Unable to update User! Errors: ' . print_r($model->getErrors(), true));
        }

        return $this->render('update', [
            'model' => $model,
            'passwordForm' => $passwordForm,
        ]);
    }

    /**
     * Deletes a User.
     *
     * @param integer $id The User's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_DELETE)) {
            return $this->redirect(['index']);
        }

        /* @var \app\models\db\User $model */
        $model = $id === null ? null : User::findOne(['id' => $id]);
        if ($model === null) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('user-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Undeletes a User.
     *
     * @param integer $id The User's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionRestore($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_DELETE)) {
            return $this->redirect(['index']);
        }

        /* @var \app\models\db\User $model */
        $model = $id === null ? null : User::findOne(['id' => $id]);
        if ($model !== null) {
            $model->restore();
        }

        return $this->redirect(['index']);
    }

    public function actionApiGenerate($id = null)
    {
        $user = User::findOne(['id' => $id]);

        if (!$user) {
            return $this->redirect(['users']);
        }

        $user->api_token = Key::generate(20);
        $user->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionApiRevoke($id = null)
    {
        $user = User::findOne(['id' => $id]);

        if (!$user) {
            return $this->redirect(['users']);
        }

        $user->api_token = null;
        $user->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Kills a User, removes it's existence from the Universe...
     * Goodbye reports, comments, activities, uploaded files.
     *
     * @param integer $id The User's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionKill($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_KILL)) {
            return $this->redirect(['index']);
        }

        /* @var \app\models\db\User $model */
        $model = $id === null ? null : User::findOne(['id' => $id]);
        if ($model === null) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->kill();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('user-kill-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Returns all data about the user for GDPR reasons
     *
     * @param integer $id The User's id
     * @return array|Response
     * @throws \Exception
     */
    public function actionFullDataExport($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_KILL)) {
            return $this->redirect(['index']);
        }

        /* @var \app\models\db\User $model */
        $model = $id === null ? null : User::findOne(['id' => $id]);
        if ($model === null) {
            return $this->redirect(['index']);
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $model->fullData();

        return $response;
    }
}
