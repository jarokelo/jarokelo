<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\MapboxLayerAsset;
use yii\helpers\Json;
use app\assets\MapLayerAsset;
use kartik\color\ColorInput;

/* @var yii\web\View $this */
/* @var app\models\db\MapLayer $model */
/* @var yii\widgets\ActiveForm $form */

MapboxLayerAsset::register($this);
MapLayerAsset::register($this);
?>

<script type="text/javascript">
    var GEOJSON = <?= Json::encode($model->data)?>;
</script>

<div class="maplayer-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'maplayer-form',
            'enableClientValidation' => true,
            'options' => ['enctype' => 'multipart/form-data'],
        ]
    ); ?>

    <?php $model->data = null; // clearing it to prevent memory exception after it's passed to Js ?>
    <?= $form->field($model, 'data')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'files[]')->fileInput(['multiple' => true])->label(Yii::t('admin', 'map-layers.supported_formats')) ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'lng')->textInput() ?>

    <?= $form->field($model, 'zoom')->textInput() ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'color')->label(Yii::t('map_layer', 'color'))->widget(ColorInput::classname(), [
        'options' => [
            'placeholder' => Yii::t('map_layer', 'select_color'),
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'label.create') : Yii::t('admin', 'label.update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div id="map" style="width: 100%; height: 400px;"></div>
<?php
$this->registerJs('initMapLayerHandler();');
