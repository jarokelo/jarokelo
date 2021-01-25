<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\models\db\City $city */
/* @var \app\models\db\Street $model */

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'streetgroup-create-ajax',
        'action' => ['city/streetgroup', 'id' => $model->id, 'cityId' => $city->id],
        'enableClientValidation' => true,
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><?= Yii::t('street', $model->isNewRecord ? 'streetgroup.create' : 'streetgroup.update') ?></h4>
    </div>

    <div class="modal-body">

        <?= $form->field($model, 'name')->textInput([]) ?>
        <?= $form->field($model, 'connectedStreets')
            ->widget(Select2::className(), [
                'options' => [
                    'multiple' => true,
                    'placeholder' => '',
                ],
                'data' => \app\models\db\Street::listStreets($city->id),
                'theme' => Select2::THEME_KRAJEE,
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>
        <?= $form->field($model, 'city_id')->hiddenInput(['value' => $city->id])->label(''); ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
