<?php

use yii\bootstrap\Html;

/* @var \app\models\db\Street $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('street', 'delete') ?></h4>
    </div>

    <div class="modal-body">
        <?= Yii::t('street', 'delete.message') ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['city/delete-street', 'id' => $model->city_id, 'sid' => $model->id], ['class' => 'btn btn-primary btn-pjax-research', 'data-search-form' => '#street-grid-view-search', 'data-modal' => '#street-delete-modal']) ?>
    </div>
</div>
