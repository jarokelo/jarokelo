<?php
/**
 * @var \Yii\Web\View $this
 * @var array $options
 */

use app\assets\MapboxAsset;
use yii\helpers\Json;
use yii\web\View;

MapboxAsset::register($this);

$this->registerJsFile(
    'https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['google']['api_key_http'] . '&libraries=places&callback=Mapbox.initMap',
    [
        'async' => true,
        'defer' => true,
        'depends' => [
            MapboxAsset::className(),
        ],
    ]
);

// overriding / setting zoom display value for const 16
$options['zoom'] = 16;

$this->registerJs('window.mapInitData = ' . Json::encode($options) . ';', View::POS_BEGIN);
