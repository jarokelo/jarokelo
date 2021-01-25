<?php

namespace app\modules\api\controllers;

use app\modules\api\components\ApiController;
use app\models\db\City;
use yii\data\Pagination;
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

    /**
     * Returns all active cities
     *
     * @return array|mixed
     */
    public function actionIndex()
    {
        $query = City::find()
            ->where([
                'status' => City::STATUS_ACTIVE,
            ])
            ->orderBy(['name' => SORT_ASC]);

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSizeLimit' => [1, 20],
            'pageSizeParam' => 'limit',
        ]);

        $list = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $items = [];
        /* @var $item \app\models\db\City */
        foreach ($list as $item) {
            $items[] = static::getCommonData($item);
        }
        return [
            'items' => $items,
            'pagination' => $pagination->getLinks(true),
        ];
    }

    /**
     * Returns details of a city
     *
     * @param $id
     * @return array|mixed
     * @throws \yii\web\HttpException when city not found or inactive
     */
    public function actionView($id)
    {
        /* @var $item \app\models\db\City */
        $item = City::find()
            ->where([
                'id' => $id,
                'status' => City::STATUS_ACTIVE,
            ])->one();

        if (!$item) {
            throw new HttpException(404, 'City not found or inactive.');
        }

        $districts = [];

        if ($item->getDistricts()->asArray()->count() > 0) {
            /* @var $district \app\models\db\District */
            foreach ($item->districts as $district) {
                $districts[] = [
                    'reports' => [
                        'count' => $district->getReports()->asArray()->count(),
                        'url' => Url::to(['reports/index', 'district' => $district->id], true),
                    ],
                ];
            }
        }

        $commonData = static::getCommonData($item);
        return ArrayHelper::merge(
            $commonData,
            [
                'districts' => $districts,
            ]
        );
    }

    /**
     * Returns the array of the common keys
     * @param $item \app\models\db\City
     * @return array
     */
    protected static function getCommonData($item)
    {
        /* @var $item \app\models\db\City */
        return [
            'id' => $item->id,
            'name' => $item->name,
            'has_districts' => $item->has_districts,
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            'url' => Url::to(['cities/view', 'id' => $item->id], true),
            'reports' => [
                'count' => $item->getReports()->asArray()->count(),
                'url' => Url::to(['reports/index', 'city' => $item->id], true),
            ],
        ];
    }
}
