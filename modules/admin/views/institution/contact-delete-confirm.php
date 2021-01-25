<?php

use yii\bootstrap\Html;

/* @var \app\models\db\Contact $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('institution', 'contact.delete') ?></h4>
    </div>

    <div class="modal-body">
        <?= Yii::t('institution', 'contact.delete.message') ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['institution/delete-contact', 'id' => $model->institution_id, 'cid' => $model->id], ['class' => 'btn btn-primary btn-pjax-delete-confirm', 'data-pjax-container' => '#institution-contacts', 'data-modal' => '#contact-delete-modal']) ?>
    </div>
</div>
