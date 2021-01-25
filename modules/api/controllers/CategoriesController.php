<?php

namespace app\modules\api\controllers;

use app\modules\api\components\ApiController;
use app\models\db\ReportCategory;

class CategoriesController extends ApiController
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    /**
     * Returns all report categories
     *
     * @return array|mixed
     */
    public function actionIndex()
    {
        return ReportCategory::find()->asArray()->all();
    }
}
