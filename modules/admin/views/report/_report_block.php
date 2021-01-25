<?php

use yii\bootstrap\Html;
use app\models\db\Report;

/* @var \app\models\db\Report $model */
/* @var int $index */
/* @var \yii\widgets\ListView $widget */
?>

<a data-pjax="0" href="<?= \yii\helpers\Url::to(['report/view', 'id' => $model->id]) ?>">
<div class="report__card row" data-report-id="<?= $model->id ?>">
    <div class="col-md-1 col-sm-2">
        <?= Html::img($model->pictureUrl(\app\models\db\ReportAttachment::SIZE_PICTURE_THUMBNAIL), ['style' => 'width: 80px; height: 80px']) ?>
    </div>
    <div class="col-md-5 col-sm-10">
        <div class="truncate"><h4><?= $model->name ?></h4>
            <span class="label label-status label-status--<?= Yii::t('const', 'report.class.' . $model->status); ?>"><?= Yii::t('const', 'report.status.' . $model->status) ?></span></div>
        <div class="fs-small"><?= $model->getUniqueName() ?></div>
        <div class="fs-small truncate">
            <?= Yii::t('report', 'block.report_time') ?> <?= Yii::$app->formatter->asDatetime($model->created_at) ?>, <?= Yii::t('report', 'block.reporter') ?> <?= ($model->user_id === null || $model->anonymous) ? Yii::t('report', 'report.anonymous') : $model->user->getFullname() ?>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="pt-13">
            <span class="glyphicon glyphicon-map-marker"
                  aria-hidden="true"></span><?= $model->getLocationName() ?>
        </div>
        <div class="pt-13">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span><?= $model->reportCategory->name ?>
        </div>

        <?php if ($model->project != Report::PROJECT_DEFAULT && isset(Report::getProjects()[$model->project])): ?>
            <div class="pt-13">
                <span class="glyphicon glyphicon-book" aria-hidden="true"></span><?= Report::getProjects()[$model->project] ?>
            </div>
        <?php endif ?>

    </div>
    <div class="col-md-3 col-sm-12">
        <div class="pt-13">
            <span class="glyphicon glyphicon-home<?= $model->institution === null ? ' hidden' : '' ?>"
                  aria-hidden="true"></span><?= $model->institution === null ? '' : $model->institution->name ?>
        </div>
        <div class="pt-13">
            <span class="glyphicon glyphicon-eye-open<?= $model->admin === null ? ' hidden' : '' ?>"
                  aria-hidden="true"></span><?= $model->admin === null ? '' : $model->admin->getFullName() ?>
        </div>
    </div>
</div>
</a>
