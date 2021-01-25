<?php

/* @var \yii\web\View $this */
use app\models\db\ReportAttachment;
use yii\helpers\Html;

/* @var \app\models\db\Report $model */

?>

<div class="compare">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('report', 'update.compare') ?></h4>
    </div>

    <div class="modal-body">
        <?php
        if (!isset($model->reportOriginal)) {
            echo Html::tag('div', Yii::t('report', 'error.missing_original_report'), ['class' => 'alert alert-danger']);
        } else { ?>
            <div class="row">
                <div class="col-md-6">
                    <h5><?= Yii::t('report', 'update.compare.current') ?></h5>
                </div>
                <div class="col-md-6">
                    <h5 class="tt-upper"><?= Yii::t('report', 'update.compare.original') ?></h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h3><?= $model->name ?></h3>
                </div>
                <div class="col-md-6">
                    <h3><?= $model->reportOriginal->name ?></h3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->getUniqueName() ?></p>
                </div>
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->getUniqueName() ?></p>
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.category') ?></h5>
                </div>
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.category') ?></h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->reportCategory->name ?></p>
                </div>
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->reportOriginal->reportCategory->name ?></p>
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.description')?></h5>
                </div>
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.description')?></h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->description ?></p>
                </div>
                <div class="col-md-6">
                    <p class="fs-medium"><?= $model->reportOriginal->description ?></p>
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.pictures')?></h5>
                </div>
                <div class="col-md-6">
                    <h5 class="fs-medium"><?= Yii::t('report', 'update.compare.pictures')?></h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php foreach ($model->reportAttachments as $picture): ?>
                        <?php if ($picture->type != ReportAttachment::TYPE_PICTURE) {
                            continue;
                        } ?>

                        <?= Html::img($picture->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_THUMBNAIL)) ?>
                    <?php endforeach ?>
                </div>
                <div class="col-md-6">
                    <?php foreach ($model->reportAttachmentOriginals as $picture_original): ?>
                        <?php if ($picture_original->type != ReportAttachment::TYPE_PICTURE) {
                            continue;
                        } ?>

                        <?= Html::img($picture_original->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_THUMBNAIL)) ?>
                    <?php endforeach ?>
                </div>
            </div>
        <?php }?>
    </div>
</div>
