<?php

use yii\bootstrap\Html;

/* @var \app\models\db\User $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('report', 'video.delete') ?></h4>
    </div>

    <div class="modal-body">
        <p>
            <?= Yii::t('report', 'video.delete.message') ?>
        </p>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['report/delete-video', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-method' => 'post', 'data-modal' => '#video-delete-modal']) ?>
    </div>
</div>
