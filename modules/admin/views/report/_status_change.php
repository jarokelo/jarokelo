<?php

use app\models\db\Report;
use app\modules\admin\models\StatusChange;

use kartik\date\DatePicker;
use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\StatusChange $model */

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'report-status-change',
        'action' => ['report/status', 'id' => $model->report->id],
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('report', 'status.edit') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'status')->widget(Select2::className(), [
            'data' => $model->statuses(),
            'options' => ['placeholder' => Yii::t('label', 'generic.choose')],
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
        <div class="hidden">
            <div id="hidden-status-desc"><?= Yii::t('report', 'status_change.hidden_status') ?></div>
        </div>
        <?= $form->field($model, 'solutionDate', ['options' => ['class' => 'hidden']])->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
        ]) ?>
        <?= $form->field($model, 'reason', ['options' => ['class' => 'hidden']])->widget(Select2::className(), [
            'data' => Report::closeReasons(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
        <?= $form->field($model, 'comment', ['options' => ['class' => 'hidden']])->textarea() ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
