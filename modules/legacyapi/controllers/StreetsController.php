<?php

namespace app\modules\legacyapi\controllers;

use app\models\db\Street;
use app\modules\legacyapi\components\ApiController;
use Yii;

class StreetsController extends ApiController
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['pageCache']['enabled'] = !YII_DEBUG;

        return $behaviors;
    }

    /**
     * Returns all streets from a city
     *
     * @return array|mixed
     */
    public function actionIndex($mesto = null)
    {
        if ($mesto === null) {
            return [
                'code' => -501,
                'message' => 'Nie je zadané ID mesta!',
            ];
        }

        $list = Street::find()
            ->where(['city_id' => $mesto])
            ->orderBy('name ASC')
            ->asArray()
            ->all();

        $items = [];
        /* @var $item \app\models\db\Street */
        foreach ($list as $item) {
            $items[] = [
                'id' => $item['id'],
                'nazov' => $item['name'],
                'lat' => $item['latitude'],
                'long' => $item['longitude'],
            ];
        }
        return $items;
    }
}
