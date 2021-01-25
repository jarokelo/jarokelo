<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\HttpCache;
use app\models\db\MapLayer;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\Json;

/**
 *
 */
class MapLayerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'only' => ['get'],
                'lastModified' => function (Action $action, $params) {
                    return MapLayer::find()->select(['updated_at'])->orderBy(['id' => SORT_DESC])->limit(1)->scalar();
                },
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionGet()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/javascript');

        /** @var MapLayer $model */
        $model = MapLayer::find()
            ->select(['data', 'color'])
            ->where(['id' => Yii::$app->request->get('id')])
            ->all();

        return 'window.MAPLAYERDATA = ' . Json::encode($model);
    }
}
