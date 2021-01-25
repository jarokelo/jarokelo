<?php

use yii\bootstrap\Html;

/* @var \app\models\db\Contact $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('pr_page', 'confirm.delete.title') ?></h4>
    </div>

    <div class="modal-body">
        <p><?= Yii::t('pr_page', 'confirm.delete.confirm') ?></p>
        <p><?= Yii::t('pr_page', 'confirm.delete.message') ?></p>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'delete'), ['pr-page/delete', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-method' => 'post']) ?>
    </div>
</div>
