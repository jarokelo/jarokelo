<?php

use app\models\db\City;
use app\models\db\Institution;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\Institution $model */

$this->title = Yii::t('label', 'new_city');

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'institution-create-ajax',
        'action' => ['institution/create'],
        'enableClientValidation' => true,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('institution', 'create') ?></h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'city_id')->widget(Select2::className(), [
            'data' => City::availableCities(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
        <?= $form->field($model, 'type')->widget(Select2::className(), [
            'data' => Institution::types(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'add'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
