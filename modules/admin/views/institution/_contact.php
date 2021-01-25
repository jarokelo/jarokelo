<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\Contact $model */
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'contact-edit-form',
        'action' => ['institution/contact', 'id' => $model->institution_id, 'cid' => $model->id],
        'options' => [
            'data-pjax' => 1,
        ],
    ]) ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('institution', $model->isNewRecord ? 'contact.create' : 'contact.update') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
