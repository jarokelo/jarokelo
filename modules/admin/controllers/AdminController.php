<?php

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use app\models\db\AdminCity;
use app\models\db\AdminPrPage;
use app\modules\admin\models\NewAdminPasswordForm;
use app\modules\admin\models\AdminForm;
use app\modules\admin\models\AdminPasswordForm;
use app\modules\admin\models\AdminSearch;
use app\modules\admin\models\PermissionForm;

use Yii;

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles Admin related actions.
 *
 * @package app\modules\admin\controllers
 */
class AdminController extends Controller
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
                'paramName' => Html::getInputName(new AdminForm(), 'image_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/admin',
            ],
            'au.thumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/admin',
            ],
            'au.fullthumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/admin',
                'useThumbs' => false,
            ],
            'au.delete' => [
                'class' => 'app\components\jqueryupload\DeleteAction',
                'uploadDest' => '@runtime/upload-tmp/admin',
            ],
        ];
    }

    /**
     * Displays the list of Admins.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new AdminSearch();

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
     * Handles the creation of a new Admin.
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_ADD)) {
            return $this->redirect(['index']);
        }

        $model = new AdminForm();
        $model->setScenario('create');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->permissions = Yii::$app->params['default_admin_permissions'];
            $model->status = Admin::STATUS_ACTIVE;

            if ($model->save()) {
                return $this->redirect(['index']);
            }

            Yii::error('Unable to create Admin! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('create', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Handles the editing of an Admin.
     *
     * @param null $id
     * @return string|Response
     * @throws \Exception
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_EDIT) && $id != Yii::$app->user->id) {
            return $this->redirect(['index']);
        }

        $filterStatus = null;
        if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN) {
            $filterStatus = [Admin::STATUS_INACTIVE, Admin::STATUS_ACTIVE];
        }

        /* @var \app\modules\admin\models\AdminForm $model */
        $model = $id === null ?
            null :
            AdminForm::find()
                ->where(['admin.id' => $id])
                ->andFilterWhere(['admin.status' => $filterStatus])
                ->with('adminCities')
                ->one();

        if ($model === null) {
            return $this->redirect(['index']);
        }

        $adminPasswordForm = new NewAdminPasswordForm(['userId' => $id]);

        if (Yii::$app->request->isPost && $adminPasswordForm->load(Yii::$app->request->post())) {
            if ($adminPasswordForm->updatePassword()) {
                Yii::$app->session->addFlash('success', Yii::t('app', 'successful_password_update'));
                return $this->redirect(Url::to(['update', 'id' => $model->id]));
            }
        }

        $origFile = $model->image_file_name;

        $model->setScenario('update');
        $model->connectedCities = $connectedCities = ArrayHelper::getColumn($model->adminCities, 'city_id');
        $model->connectedPrPages = $connectedPrPages = ArrayHelper::getColumn($model->adminPrPages, 'pr_page_id');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $newFile = $model->image_file_name;

            if (($newPicture = $model->isAttributeChanged('image_file_name')) && !empty($newFile)) {
                $parts = explode('.', $newFile);
                $ext = end($parts);

                $model->image_file_name = $model->id . '-' . uniqid() . '.' . $ext;
            }

            if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN) {
                if ($model->isAttributeChanged('status') && $model->status == Admin::STATUS_SUPER_ADMIN) {
                    $model->status = $model->getOldAttribute('status');
                }

                $model->connectedCities = $connectedCities; // Overwrite connectedCities, so the non-super admins can't edit it
                $model->connectedPrPages = $connectedPrPages;
            }

            if ($model->save()) {
                if (!is_array($model->connectedCities)) {
                    $model->connectedCities = [];
                }
                if (!is_array($model->connectedPrPages)) {
                    $model->connectedPrPages = [];
                }

                $intersectionCity = array_intersect($model->connectedCities, $connectedCities);

                AdminCity::deleteAll([
                    'AND',
                    ['admin_id' => $model->id],
                    ['NOT IN', 'city_id', $intersectionCity],
                ]);

                foreach ($model->connectedCities as $cityId) {
                    if (in_array($cityId, $intersectionCity)) {
                        continue;
                    }

                    $adminCity = new AdminCity();
                    $adminCity->admin_id = $model->id;
                    $adminCity->city_id = $cityId;
                    $adminCity->save();
                }

                $intersectionPrPage = array_intersect($model->connectedPrPages, $connectedPrPages);

                AdminPrPage::deleteAll([
                    'AND',
                    ['admin_id' => $model->id],
                    ['NOT IN', 'pr_page_id', $intersectionPrPage],
                ]);

                foreach ($model->connectedPrPages as $prPageId) {
                    if (in_array($prPageId, $intersectionPrPage)) {
                        continue;
                    }

                    $adminPrPage = new AdminPrPage();
                    $adminPrPage->admin_id = $model->id;
                    $adminPrPage->pr_page_id = $prPageId;
                    $adminPrPage->save();
                }

                if ($newPicture) {
                    if (!is_dir(Yii::getAlias('@app/web/files/admin/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/admin/thumb/'), 0777, true);
                    }

                    if (!empty($origFile)) {
                        @unlink(Yii::getAlias("@app/web/files/admin/{$origFile}"));
                        @unlink(Yii::getAlias("@app/web/files/admin/thumb/{$origFile}"));
                    }

                    if (!empty($newFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/admin/{$newFile}"), Yii::getAlias("@app/web/files/admin/{$model->image_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/admin/thumb/{$newFile}"), Yii::getAlias("@app/web/files/admin/thumb/{$model->image_file_name}"));
                    }
                }
                Yii::$app->session->addFlash('success', Yii::t('admin', 'update.success'));
                return $this->redirect(['index']);
            }

            Yii::error('Unable to update Admin! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('update', [
                'model' => $model,
                'adminPasswordForm' => $adminPasswordForm,
            ]);
        }

        Url::remember(Yii::$app->request->referrer);
        return $this->render('update', [
            'model' => $model,
            'adminPasswordForm' => $adminPasswordForm,
        ]);
    }

    /**
     * Displays the Admin's own profile page.
     *
     * @return string
     */
    public function actionProfile()
    {
        Url::remember(Yii::$app->request->referrer);

        return $this->render('update', [
            'model' => AdminForm::findOne(['id' => Yii::$app->user->id]),
            'edit' => false,
        ]);
    }

    /**
     * Displays the Admin's password change form.
     *
     * @return string
     */
    public function actionPassword()
    {
        $model = AdminPasswordForm::findOne(['id' => Yii::$app->user->id]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->hashPassword($model->new_password);
                if ($model->save()) {
                    Yii::$app->session->addFlash('success', Yii::t('app', 'successful_password_update'));
                    return $this->redirect(['/admin/task/index']);
                }
            }
        }

        return $this->render('password', [
            'model' => $model,
        ]);
    }

    /**
     * Handles the Permission settings.
     *
     * @param integer $id the Admin's id
     * @return string|Response
     */
    public function actionPermission($id = null)
    {
        if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN) {
            return $this->redirect(['admin/update', 'id' => $id]);
        }

        if ($id == Yii::$app->user->id) {
            return $this->redirect(['admin/update', 'id' => $id]);
        }

        /* @var \app\modules\admin\models\AdminForm $admin */
        $admin = $id === null ? null : AdminForm::findOne(['id' => $id]);
        if ($admin === null) {
            return $this->redirect(['index']);
        }

        $model = new PermissionForm();
        $model->loadAdmin($admin);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->applyChanges($admin)) {
                if (Yii::$app->request->isPjax) {
                    return $this->renderPartial('update', [
                        'model' => $admin,
                    ]);
                }

                return $this->redirect(['admin/update', 'id' => $admin->id]);
            }

            Yii::error('Unable to update Admin\'s permissions! Permission errors: ' . print_r($model->getErrors(), true) . ' | Admin errors: ' . print_r($admin->getErrors(), true));
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('permission', [
                    'model' => $model,
                    'admin' => $admin,
                ]),
            ];
        }

        return $this->redirect(['admin/update', 'id' => $admin->id]);
    }

    /**
     * Handles the deletion of an Admin.
     *
     * @param integer $id the about to be deleted Admin's id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_DELETE)) {
            return $this->redirect(['index']);
        }

        $filterStatus = null;
        if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN) {
            $filterStatus = [Admin::STATUS_INACTIVE, Admin::STATUS_ACTIVE];
        }

        /* @var \app\models\db\Admin $model */
        $model = $id === null ? null : Admin::find()->where(['id' => $id])->andFilterWhere(['status' => $filterStatus])->one();
        if ($model === null || $model->id == Yii::$app->user->id) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $success = $model->delete(); // TODO: soft delete only?

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
                'html' => $this->renderAjax('admin-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Undeletes an Admin.
     *
     * @param integer $id Admin's id
     * @return Response
     * @throws \Exception
     */
    public function actionRestore($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_DELETE)) {
            return $this->redirect(['index']);
        }

        $filterStatus = null;
        if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN) {
            $filterStatus = [Admin::STATUS_INACTIVE, Admin::STATUS_ACTIVE];
        }

        /* @var \app\models\db\Admin $model */
        $model = $id === null ? null : Admin::find()->where(['id' => $id])->andFilterWhere(['status' => $filterStatus])->one();
        if ($model !== null && $model->id != Yii::$app->user->id) {
            $model->restore();
        }

        return $this->redirect(['index']);
    }
}
