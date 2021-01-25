<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.11.
 * Time: 15:28
 */

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use app\models\db\AdminPrPage;
use app\models\db\Institution;
use app\models\db\PrPage;
use app\models\db\PrPageNews;
use Yii;

use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Handles Pr pages related actions.
 *
 * @package app\modules\admin\controllers
 */
class PrPageController extends Controller
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
            'au.upload.logo' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new PrPage(), 'logo_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/pr-page',
            ],
            'au.upload.cover' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new PrPage(), 'cover_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/pr-page',
            ],
            'au.thumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page',
            ],
            'au.fullthumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page',
                'useThumbs' => false,
            ],
            'au.delete' => [
                'class' => 'app\components\jqueryupload\DeleteAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page',
            ],
        ];
    }

    /**
     * Displays the list of Pr pages.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        $model = new PrPage();

        if ($model->countPrPagesByAdmin() > 1) {
            $viewData = [
                'dataProvider' => $model->search(),
                'model' => $model,
            ];

            return $this->render('index', $viewData);
        } else {
            return $this->redirect(['pr-page/update', 'id' => current(Yii::$app->user->identity->getAdminPrPagesAsArray())['pr_page_id']]);
        }
    }

    /**
     * Handles the creation of a new Pr page by an Institution.
     *
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        /** @var Institution $institution */
        $institution = $id === null ? null : Institution::find()->where(['id' => $id])->with('city')->one();
        if ($institution === null) {
            return $this->redirect(['institution/index']);
        }

        $model = new PrPage();
        $model->institution_id = $id;
        $model->title = $institution->name . ' "pr oldala"';
        $model->slug = $model->institution->slug . '-pr-oldala';
        $model->introduction = $institution->name . ' pr oldal bemutatkozó tartalma.';
        if ($model->save()) {
            if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
                return $this->redirect(['institution/index']);
            }
        }

        return $this->redirect(['institution/index']);
    }

    /**
     * Handles the edit of a Pr page.
     *
     * @param integer $id The Pr page's id
     * @return string|Response
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        if (!$id) {
            return $this->redirect(['institution/index']);
        }

        /* @var \app\models\db\PrPage $model */
        $model = $id === null ?
            null :
            PrPage::findOne(['id' => $id]);

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        $origLogoFile = $model->logo_file_name;
        $origCoverFile = $model->cover_file_name;

        if ($model->load(Yii::$app->request->post())) {
            $newLogoFile = $model->logo_file_name;
            $newCoverFile = $model->cover_file_name;

            if (($newLogoPicture = $model->isAttributeChanged('logo_file_name')) && !empty($newLogoFile)) {
                $parts = explode('.', $newLogoFile);
                $ext = end($parts);

                $model->logo_file_name = $model->id . '-' . uniqid() . '.' . $ext;
            }

            if (($newCoverPicture = $model->isAttributeChanged('cover_file_name')) && !empty($newCoverFile)) {
                $parts = explode('.', $newCoverFile);
                $ext = end($parts);

                $model->cover_file_name = $model->id . '-' . uniqid() . '.' . $ext;
            }

            if ($model->save()) {
                if ($newLogoPicture) {
                    if (!is_dir(Yii::getAlias('@app/web/files/pr-page/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/pr-page/thumb/'), 0777, true);
                    }

                    if (!empty($origLogoFile)) {
                        @unlink(Yii::getAlias("@app/web/files/pr-page/{$origLogoFile}"));
                        @unlink(Yii::getAlias("@app/web/files/pr-page/thumb/{$origLogoFile}"));
                    }

                    if (!empty($newLogoFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page/{$newLogoFile}"), Yii::getAlias("@app/web/files/pr-page/{$model->logo_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page/thumb/{$newLogoFile}"), Yii::getAlias("@app/web/files/pr-page/thumb/{$model->logo_file_name}"));
                    }
                }

                if ($newCoverPicture) {
                    if (!is_dir(Yii::getAlias('@app/web/files/pr-page/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/pr-page/thumb/'), 0777, true);
                    }

                    if (!empty($origCoverFile)) {
                        @unlink(Yii::getAlias("@app/web/files/pr-page/{$origCoverFile}"));
                        @unlink(Yii::getAlias("@app/web/files/pr-page/thumb/{$origCoverFile}"));
                    }

                    if (!empty($newCoverFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page/{$newCoverFile}"), Yii::getAlias("@app/web/files/pr-page/{$model->cover_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page/thumb/{$newCoverFile}"), Yii::getAlias("@app/web/files/pr-page/thumb/{$model->cover_file_name}"));
                    }
                }
                return $this->redirect(['pr-page/update', 'id' => $model->id]);
            }

            Yii::error('Unable to update Pr page! Errors: ' . print_r($model->getErrors(), true));
        }

        $viewData = [
            'model' => $model,
        ];

        return $this->render('update', $viewData);
    }



    /**
     * Handles the deletion of a Pr page.
     *
     * @param integer $id the about to be deleted Pr page's id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_DELETE)) {
            return $this->goHome();
        }

        /* @var \app\models\db\PrPage $model */
        $model = $id === null ? null : PrPage::find()->where(['id' => $id])->one();
        if ($model === null || $model->id == Yii::$app->user->id) {
            return $this->redirect(['institution/index']);
        }

        if (Yii::$app->request->isPost) {

            /** @var PrPageNews $prPageNewModal */
            foreach ($model->getPrPageNews() as $prPageNew) {
                $prPageNewModal = PrPageNews::findOne($prPageNew['id']);
                $prPageNewModal->delete();
            }

            /** @var AdminPrPage $adminPrPageModel */
            foreach ($model->getAdminPrPages() as $adminPrPage) {
                $adminPrPageModel = AdminPrPage::findOne($adminPrPage['pr_page_id']);
                $adminPrPageModel->delete();
            }

            $success = $model->delete();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return [
                    'success' => $success,
                ];
            }

            return $this->redirect(['institution/index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('pr-page-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['index']);
    }
}
