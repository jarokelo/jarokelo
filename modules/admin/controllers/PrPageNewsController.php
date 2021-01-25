<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.17.
 * Time: 15:32
 */

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use app\models\db\PrPage;
use app\models\db\PrPageNews;
use app\modules\admin\models\PrPageNewsForm;
use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\base\Exception;
use yii\helpers\Html;
use app\components\ActiveForm;

/**
 * Handles Pr page news related actions.
 *
 * @package app\modules\admin\controllers
 */
class PrPageNewsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'au.upload' => [
                'class' => 'app\components\jqueryupload\UploadAction',
                'paramName' => Html::getInputName(new PrPageNewsForm(), 'image_file_name'),
                'createDirs' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                'maxSize' => 5 * 1024 * 1024,
                'preview' => [75, 75],
                'uploadDest' => '@runtime/upload-tmp/pr-page-news',
            ],
            'au.thumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page-news',
            ],
            'au.fullthumb' => [
                'class' => 'app\components\jqueryupload\ThumbAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page-news',
                'useThumbs' => false,
            ],
            'au.delete' => [
                'class' => 'app\components\jqueryupload\DeleteAction',
                'uploadDest' => '@runtime/upload-tmp/pr-page-news',
            ],
        ];
    }

    /**
     * Displays the list of News
     *
     * @param null $id Pr page Id
     * @return string
     */
    public function actionIndex($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        $model = new PrPageNews();
        $dataProvider = $model->getAllNewsByPrPageId($id);

        $prPageModel = PrPage::findOne(['id' => $id]);

        /* @var $dataProvider->pagination yii\data\Pagination */
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->pagination->pageSizeParam = 'limit';

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'id' => $id,
            'prPageModel' => $prPageModel,
        ]);
    }

    /**
     * Handles the editing of a News.
     *
     * @param null $id The Id of the News
     * @return string|Response
     */
    public function actionUpdate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        /* @var \app\modules\admin\models\PrPageNewsForm $model */
        $model = $id === null ?
            null :
            PrPageNewsForm::find()
                ->where(['pr_page_news.id' => $id])
                ->one();

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        $origFile = $model->image_file_name;
        $model->setScenario(PrPageNews::SCENARIO_UPDATE);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $newFile = $model->image_file_name;
            $model->published_at = strtotime($model->published_date . ' 00:00:00');

            if (($newPicture = $model->isAttributeChanged('image_file_name')) && !empty($newFile)) {
                $parts = explode('.', $newFile);
                $ext = end($parts);

                $model->image_file_name = $id . '-' . uniqid() . '.' . $ext;
            }

            if ($model->save()) {
                if ($newPicture) {
                    if (!is_dir(Yii::getAlias('@app/web/files/pr-page-news/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/pr-page-news/thumb/'), 0777, true);
                    }

                    if (!empty($origFile)) {
                        @unlink(Yii::getAlias("@app/web/files/pr-page-news/{$origFile}"));
                        @unlink(Yii::getAlias("@app/web/files/pr-page-news/thumb/{$origFile}"));
                    }

                    if (!empty($newFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page-news/{$newFile}"), Yii::getAlias("@app/web/files/pr-page-news/{$model->image_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page-news/thumb/{$newFile}"), Yii::getAlias("@app/web/files/pr-page-news/thumb/{$model->image_file_name}"));
                    }
                }
                return $this->redirect(['pr-page-news/index', 'id' => $model->pr_page_id]);
            }

            Yii::error('Unable to update news! Errors: ' . print_r($model->getErrors(), true));
        }

        $model->published_date = date('Y-m-d', $model->published_at);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Handles the creation of a new News.
     *
     * @param null $id The Pr page's id
     * @return array|Response
     */
    public function actionCreate($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        $model = new PrPageNewsForm();
        $model->setScenario(PrPageNews::SCENARIO_CREATE);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $newFile = $model->image_file_name;
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            $model->published_at = strtotime($model->published_date . ' 00:00:00');
            if ($model->save()) {
                if (!empty($newFile)) {
                    $parts = explode('.', $model->image_file_name);
                    $ext = end($parts);

                    $model->image_file_name = $model->id . '-' . uniqid() . '.' . $ext;

                    if (!$model->save()) {
                        Yii::error('Unable to create news! Errors: ' . print_r($model->getErrors(), true));
                    }

                    if (!is_dir(Yii::getAlias('@app/web/files/pr-page-news/thumb/'))) {
                        @mkdir(Yii::getAlias('@app/web/files/pr-page-news/thumb/'), 0777, true);
                    }

                    if (!empty($newFile)) {
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page-news/{$newFile}"), Yii::getAlias("@app/web/files/pr-page-news/{$model->image_file_name}"));
                        @rename(Yii::getAlias("@runtime/upload-tmp/pr-page-news/thumb/{$newFile}"), Yii::getAlias("@app/web/files/pr-page-news/thumb/{$model->image_file_name}"));
                    }
                }
                return $this->redirect(['pr-page-news/index', 'id' => $model->pr_page_id]);
            }

            Yii::error('Unable to create news! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            $model->pr_page_id = $id;
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model->published_date = date('Y-m-d');

            return [
                'success' => true,
                'html' => $this->renderAjax('create', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['pr-page-news/index', 'id' => $id]);
    }

    /**
     * Handles the deletion of a News.
     *
     * @param null $id The id of the News
     * @return array|Response
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionDelete($id = null)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            return $this->goHome();
        }

        $transaction = Yii::$app->db->beginTransaction();

        /* @var \app\models\db\PrPageNews $model */
        $model = $id === null ?
            null :
            PrPageNews::find()
                ->where(['pr_page_news.id' => $id])
                ->one();

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        $prPageId = $model->pr_page_id;

        try {
            if (Yii::$app->request->isPost) {
                $success = $model->delete();

                $transaction->commit();

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return [
                        'success' => $success,
                    ];
                }

                return $this->redirect(['pr-page-news/index', 'id' => $prPageId]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            if ($e instanceof \yii\db\Exception) {
                Yii::$app->session->setFlash('error', Yii::t('error', Inflector::slug($e->getName())));
            } else {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('pr-page-news-delete-confirm', [
                    'model' => $model,
                ]),
            ];
        }

        return $this->redirect(['pr-page-news/index', 'id' => $prPageId]);
    }

    /**
     * Set Highlight status true, if it is false and false, if it is true.
     * @param null $id The Id of the News
     * @return array|Response
     */
    public function actionHighlight($id = null)
    {
        /* @var \app\models\db\PrPageNews $model */
        $model = $id === null ?
            null :
            PrPageNews::find()
                ->where(['pr_page_news.id' => $id])
                ->one();

        if ($model === null) {
            return $this->redirect(['institution/index']);
        }

        if (Yii::$app->request->isPost) {
            if ($model->setHighlightedNews($id)) {
                return $this->redirect(['pr-page-news/index', 'id' => $model->pr_page_id]);
            }

            Yii::error('Unable to change highligted status! Errors: ' . print_r($model->getErrors(), true));
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'success' => true,
                'html' => $this->renderAjax('pr-page-news-highlight-confirm', [
                    'model' => $model,
                    'highlightedModel' => PrPageNews::findOne(['highlighted' => PrPageNews::HIGHLIGHTED_TRUE]),
                ]),
            ];
        }

        return $this->redirect(['pr-page-news/index', 'id' => $model->pr_page_id]);
    }

    /**
     * Set status Active.
     *
     * @param null $id The Id of the News.
     * @return Response
     */
    public function actionActivate($id = null)
    {
        /* @var \app\models\db\PrPageNews $model */
        $model = $id === null ? null : PrPageNews::find()->where(['id' => $id])->one();
        if ($model !== null && $model->id != Yii::$app->user->id) {
            $model->activate();
        }

        return $this->redirect(['index', 'id' => $model->pr_page_id]);
    }

    /**
     * Set status Inactive.
     *
     * @param null $id The Id of the News.
     * @return Response
     */
    public function actionInactivate($id = null)
    {
        /* @var \app\models\db\PrPageNews $model */
        $model = $id === null ? null : PrPageNews::find()->where(['id' => $id])->one();
        if ($model !== null && $model->id != Yii::$app->user->id) {
            if ($model->isHighlighted()) {
                $model->highlighted = PrPageNews::HIGHLIGHTED_FALSE;
            }
            $model->inactivate();
        }

        return $this->redirect(['index', 'id' => $model->pr_page_id]);
    }
}
