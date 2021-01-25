<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\db\MapLayer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'map-layers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kml-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'label.update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'label.delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'label.confirm_delete'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'lat',
                'label' => Yii::t('admin', 'map-layers.lat'),
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'map-layers.name'),
            ],
            [
                'attribute' => 'lng',
                'label' => Yii::t('admin', 'map-layers.lng'),
            ],
            'zoom',
            [
                'attribute' => 'created_by',
                'label' => Yii::t('admin', 'created_by'),
                'format' => 'raw',
                'value' => $model->createdBy ? Html::a($model->createdBy->getFullName(), Url::to(['admin/update', 'id' => $model->createdBy->id])) : null,
            ],
            [
                'attribute' => 'updated_by',
                'label' => Yii::t('admin', 'updated_by'),
                'format' => 'raw',
                'value' => $model->updatedBy ? Html::a($model->updatedBy->getFullName(), Url::to(['admin/update', 'id' => $model->updatedBy->id])) : null,
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('admin', 'created_at'),
                'value' => $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : null,
            ],
            [
                'attribute' => 'updated_at',
                'label' => Yii::t('admin', 'updated_at'),
                'value' => $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : null,
            ],
            [
                'attribute' => 'color',
                'label' => Yii::t('map_layer', 'color'),
                'format' => 'raw',
                'value' => $model->color ? Html::tag('div', '', ['style' => ['background-color' => $model->color, 'width' => '15px', 'height' => '15px']]) : '',
            ],
        ],
    ]) ?>

</div>
