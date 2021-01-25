<?php

namespace app\modules\legacyapi\controllers;

use app\models\db\Report;
use app\modules\legacyapi\components\ApiController;
use app\models\db\City;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\helpers\Url;

class CitiesController extends ApiController
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
     * Returns all active cities
     *
     * @return array|mixed
     */
    public function actionIndex()
    {
        $list = City::find()
            ->where([
                'status' => City::STATUS_ACTIVE,
            ])
            ->orderBy('name ASC')
            ->asArray()
            ->all();

        $items = [];
        /* @var $item \app\models\db\City */
        foreach ($list as $item) {
            $items[] = [
                'id' => $item['id'],
                'nazov' => $item['name'],
                'mestske_casti' => $item['has_districts'],
                'lat' => $item['latitude'],
                'long' => $item['longitude'],
                'list_url' => Url::to(['reports/index', 'mesto' => $item['id']], true),
                'detail_url' => Url::to(['cities/view', 'mesto' => $item['id']], true),
                'pocet_podnetov' => Report::find()->where(['city_id' => $item['id']])->filterAvailable()->asArray()->count(),
            ];
        }
        return $items;
    }

    /**
     * Returns details of a city
     *
     * @param $mesto
     * @return array|mixed
     * @throws \yii\web\HttpException when city not found or inactive
     */
    public function actionView($mesto = null)
    {
        /* @var $item \app\models\db\City */
        $item = City::find()
            ->where([
                'id' => $mesto,
                'status' => City::STATUS_ACTIVE,
            ])->one();

        if ($item === null) {
            return [
                'list_url' => null,
                'pocet_podnetov' => 0,
                'id' => null,
                'nazov' => null,
                'lat' => null,
                'long' => null,
                'mestske_casti' => null,
            ];
        }

        $districts = [];
        if ($item->getDistricts()->orderBy(['id' => SORT_ASC])->asArray()->count() > 0) {
            /* @var $district \app\models\db\District */
            foreach ($item->districts as $district) {
                $districts[] = [
                    'id' => $district->id,
                    'nazov' => $district->name,
                    'list_url' => Url::to(['reports/index', 'mestska_cast' => $district->id], true),
                    'pocet_podnetov' => $district->getReports()->asArray()->count(),
                ];
            }
        }

        return [
            'list_url' => Url::to(['reports/index', 'mesto' => $item->id], true),
            'pocet_podnetov' => $item->getReports()->asArray()->count(),
            'id' => $item->id,
            'nazov' => $item->name,
            'lat' => $item->latitude,
            'long' => $item->longitude,
            'mestske_casti' => $districts,
        ];
    }
}
