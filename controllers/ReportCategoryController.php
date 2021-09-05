<?php

namespace app\controllers;

use app\models\db\ReportCategory;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class ReportCategoryController extends Controller
{
    /**
     * @return array
     */
    public function actionGetTaxonomyByCategoryId()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isGet) {
            return [];
        }

        $reportCategory = Yii::$app->request->get('report_category');

        if (!$reportCategory) {
            return [];
        }
        /** @var ReportCategory $model */
        $model = ReportCategory::find()
            ->where(['id' => $reportCategory])
            ->one();

        if (!$model) {
            return [];
        }

        return $model->getTaxonomyRelationList();
    }
}
