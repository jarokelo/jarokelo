<?php

use yii\bootstrap\Html;

/* @var \app\models\db\Rule $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('rule', 'delete') ?></h4>
    </div>

    <div class="modal-body">
        <?= Yii::t('rule', 'delete.message') ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['city/delete-rule', 'id' => $model->city_id, 'rid' => $model->id], ['class' => 'btn btn-primary btn-pjax-delete-confirm', 'data-pjax-container' => '#city-rules', 'data-modal' => '#rule-delete-modal']) ?>
    </div>
</div>
