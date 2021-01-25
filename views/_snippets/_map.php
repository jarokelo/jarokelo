<?php
use app\assets\MapAsset;
use yii\helpers\Json;
use yii\web\View;

MapAsset::register($this);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['google']['api_key_http'] . '&libraries=places', ['async' => true, 'defer' => true, 'depends' => [MapAsset::className()]]);

$this->registerJs('window.mapInitData = ' . Json::encode($options) . ';', View::POS_BEGIN);
