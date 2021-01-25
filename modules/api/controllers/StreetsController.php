<?php

namespace app\modules\api\controllers;

use app\models\db\Street;
use app\modules\api\components\ApiController;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

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

    /**
     * Returns all streets from a city
     *
     * @param $id
     *
     * @return array|mixed
     */
    public function actionView($id)
    {
        $query = Street::find()
            ->where(['city_id' => $id])
            ->orderBy('name ASC');

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSizeLimit' => [1, 20],
            'pageSizeParam' => 'limit',
        ]);
        $list = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $items = [];
        /* @var $item \app\models\db\Street */
        foreach ($list as $item) {
            $items[] = static::getCommonData($item);
        }
        return [
            'items' => $items,
            'pagination' => $pagination->getLinks(true),
        ];
    }

    /**
     * Returns the array of the common keys
     * @param $item \app\models\db\Street
     * @return array
     */
    protected static function getCommonData($item)
    {
        /* @var $item \app\models\db\Street */
        return [
            'id' => $item->id,
            'name' => $item->name,
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            'district' => [
                'id' => $item->district_id,
                'name' => ArrayHelper::getValue($item, 'district.name'),
            ],
        ];
    }
}
