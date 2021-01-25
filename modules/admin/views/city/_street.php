<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var \yii\web\View $this */
/* @var \app\models\db\City $city */
/* @var \app\models\db\Street $model */

echo $this->render('@app/views/_snippets/_map', [
    'options' => [
        'zoom' => $model->isNewRecord ? 11 : 15,
        'selectors' => [
            'map'           => '#map',
            'latitude'      => '#street-latitude',
            'longitude'     => '#street-longitude',
            'user_location' => '#map-search',
            'street_name'   => '#street-name',
            'post_code'     => '#street-postcode',
        ],
        'center' => [
            'lat' => \app\models\db\Report::formatCoordinate($model->latitude?: $city->latitude),
            'lng' => \app\models\db\Report::formatCoordinate($model->longitude?: $city->longitude),
        ],
        'locationChangeHandler' => true,
    ],
]);
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'street-create-ajax',
        'action' => ['city/street', 'id' => $city->id, 'sid' => $model->id],
        'enableClientValidation' => true,
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><?= Yii::t('street', $model->isNewRecord ? 'create' : 'update') ?></h4>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label for="map-search" class="control-label"><?= Yii::t('street', 'search') ?></label>
            <?= Html::input('text', 'map-search', '', ['id' => 'map-search', 'class' => 'form-control']) ?>
        </div>

        <?= $form->field($model, 'name')->textInput(['readonly' => true]) ?>
        <?= $form->field($model, 'district_id')->widget(Select2::className(), [
            'data' => ['' => ''] + $city->getAvailableDistricts(),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'options' => ArrayHelper::map(
                    $city->getDistrictNumbers(),
                    'district_id',
                    function ($elem) {
                        return [
                            'data-number' => $elem['number'],
                        ];
                    }
                ),
            ],
        ]) ?>
        <?= $form->field($model, 'latitude')->textInput() ?>
        <?= $form->field($model, 'longitude')->textInput() ?>
        <?= Html::hiddenInput(null, null, ['id' => 'street-postcode']) ?>

        <div id="map" style="width: 100%; height: 300px; margin: 0; padding: 0;"></div>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
