<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\District $model */

echo $this->render('@app/views/_snippets/_map', [
    'options' => [
        'zoom' => $model->isNewRecord ? 11 : 15,
        'selectors' => [
            'map'           => '#map',
            'latitude'      => '#district-latitude',
            'longitude'     => '#district-longitude',
            'user_location' => '#map-search',
        ],
        'center' => [
            'lat' => \app\models\db\Report::formatCoordinate($model->latitude?: $model->city->latitude),
            'lng' => \app\models\db\Report::formatCoordinate($model->longitude?: $model->city->longitude),
        ],
        'locationChangeHandler' => true,
    ],
]);
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'district-create-ajax',
        'action' => ['city/district', 'id' => $model->city_id, 'did' => $model->id],
        'enableClientValidation' => true,
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('district', $model->isNewRecord ? 'create' : 'update') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'short_name')->textInput() ?>
        <?= $form->field($model, 'name_filter')->textInput()->hint(Yii::t('district', 'name_filter.hint')) ?>
        <?= $form->field($model, 'article')->textInput()->hint(Yii::t('district', 'article.hint')) ?>
        <?= $form->field($model, 'number')->textInput() ?>
        <?= $form->field($model, 'latitude')->textInput() ?>
        <?= $form->field($model, 'longitude')->textInput() ?>
        <div class="form-group">
            <label for="map-search" class="control-label"><?= Yii::t('street', 'search') ?></label>
            <?= Html::input('text', 'map-search', '', ['id' => 'map-search', 'class' => 'form-control']) ?>
        </div>
        <div id="map" style="width: 100%; height: 300px; margin: 0; padding: 0;"></div>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
