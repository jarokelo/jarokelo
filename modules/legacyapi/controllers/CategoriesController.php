<?php

namespace app\modules\legacyapi\controllers;

use app\modules\legacyapi\components\ApiController;
use app\models\db\ReportCategory;
use yii\helpers\ArrayHelper;

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

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['pageCache']['enabled'] = !YII_DEBUG;

        return $behaviors;
    }

    /**
     * Returns all report categories
     *
     * @return array|mixed
     */
    public function actionIndex()
    {
        $list = ReportCategory::find()->orderBy('name')->asArray()->all();

        $items = [];
        foreach ($list as $item) {
            $items[] = [
                'id' => $item['id'],
                'kategoria' => $item['name'],
            ];
        }
        return $items;
    }
}
