<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\MapLayer;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'map-layers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kml-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'label.create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'map-layers.name'),
            ],
            [
                'attribute' => 'lat',
                'label' => Yii::t('admin', 'map-layers.lat'),
            ],
            [
                'attribute' => 'lng',
                'label' => Yii::t('admin', 'map-layers.lng'),
            ],
            [
                'attribute' => 'color',
                'label' => Yii::t('map_layer', 'color'),
                'format' => 'raw',
                'value' => function (MapLayer $item) {
                    if ($item->color) {
                        return Html::tag('div', '', ['style' => ['background-color' => $item->color, 'width' => '15px', 'height' => '15px']]);
                    }

                    return null;
                },
            ],
            'zoom',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
