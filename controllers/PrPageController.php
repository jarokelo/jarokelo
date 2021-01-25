<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.15.
 * Time: 16:15
 */

namespace app\controllers;

use app\models\db\PrPageNews;
use app\models\db\Report;
use app\models\db\search\ReportSearch;
use yii\web\HttpException;
use app\models\db\PrPage;
use Yii;
use yii\web\Controller;
use app\components\Header;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * Handles Pr page related actions
 *
 * Class PrPageController
 * @package app\controllers
 */
class PrPageController extends Controller
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
     * Renders the Pr page
     *
     * @param null $slug
     * @return string
     * @throws HttpException
     */
    public function actionView($slug = null)
    {
        $model = PrPage::findOne(['slug' => $slug, 'status' => PrPage::STATUS_PUBLIC]);
        if (!$model) {
            throw new HttpException(404, 'City not found or inactive.');
        }

        $dataProvider = $model->reportsOnMap();

        $newsModel = new PrPageNews();
        $news = $newsModel->getPublishedByPrPageId($model->id);

        /* @var $dataProvider->pagination yii\data\Pagination */
        $dataProvider->pagination->pageSize = 30;
        $dataProvider->pagination->pageSizeParam = 'limit';

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'news' => $news,
        ]);
    }

    /**
     * Renders the current News
     *
     * @param null $id
     * @return array|bool
     */
    public function actionShow($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            return false;
        }

        /** @var PrPageNews $news */
        $news = PrPageNews::findOne($id);

        if ($news === null) {
            return false;
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('@app/views/pr-page/_show-news', [
                'model' => $news,
                'title' => $news->title,
            ]),
        ];
    }
}
