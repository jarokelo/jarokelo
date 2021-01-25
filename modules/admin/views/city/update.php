<?php

use app\models\db\City;
use app\models\db\Report;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\City $model */
/* @var View $this */

$this->title = Yii::t('label', 'update_city');

echo $this->render('@app/views/_snippets/_map', [
    'options' => [
        'types' => ['(cities)'],
        'componentRestrictions' => ['country' => 'hu'],
        'zoom' => 7,
        'selectors' => [
            'map'           => '#map',
            'latitude'      => '#city-latitude',
            'longitude'     => '#city-longitude',
            'user_location' => '#map-search',
        ],
        'center' => [
            'lat' => Report::formatCoordinate($model->latitude),
            'lng' => Report::formatCoordinate($model->longitude),
        ],
        'locationChangeHandler' => true,
    ],
]);
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'city-update-ajax',
        'action' => ['city/update', 'id' => $model->id],
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('city', 'update') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'name_filter')->textInput()->hint(Yii::t('city', 'name_filter.hint')) ?>
        <?= $form->field($model, 'has_districts')->radioList(City::hasDistrict()) ?>
        <?= $form->field($model, 'status')->radioList(City::statuses()) ?>
        <?= $form->field($model, 'email_address')->hiddenInput([
            'id' => 'email_address',
        ])->label(false) ?>
        <?= $form->field($model, 'email_address')->textInput([
            'id' => 'email_address_fake',
            'name' => 'email_address_fake',
            'autocomplete' => 'off',
            'disabled' => 'disabled',
        ])->hint(Yii::t('city', 'email_address-hint')) ?>
        <?= $this->renderFile(\Yii::getAlias('@app/views/gmail/store.php')) ?>
        <div class="form-group">
            <label for="map-search" class="control-label"><?= Yii::t('street', 'search') ?></label>
            <?= Html::input('text', 'map-search', '', ['id' => 'map-search', 'class' => 'form-control']) ?>
        </div>

        <?= $form->field($model, 'latitude')->textInput() ?>
        <?= $form->field($model, 'longitude')->textInput() ?>

        <div id="map" style="width: 100%; height: 300px; margin: 0; padding: 0;"></div>
    </div>


    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
