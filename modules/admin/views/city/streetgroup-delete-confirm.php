<?php

use yii\bootstrap\Html;

/* @var \app\models\db\StreetGroup $model */
/* @var \app\models\db\City $city */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('street', 'streetgroup.delete') ?></h4>
    </div>

    <div class="modal-body">
        <?= Yii::t('street', 'streetgroup.delete.message') ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['city/delete-streetgroup', 'cityId' => $city->id, 'id' => $model->id], ['class' => 'btn btn-primary btn-pjax-research', 'data-search-form' => '#streetgroup-grid-view-search', 'data-modal' => '#streetgroup-delete-modal']) ?>
    </div>
</div>
