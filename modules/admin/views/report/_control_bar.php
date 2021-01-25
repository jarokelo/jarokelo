<?php

use app\models\db\Admin;
use app\models\db\Report;

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \app\models\db\Report $report */

?>

<div>
    <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)): ?>
        <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-random']) . ' ' . Yii::t('report', 'control.status'), '#', ['class' => 'btn btn-primary btn-change-status btn-modal-content', 'data-modal' => '#status-change-modal', 'data-url' => Url::to(['report/status', 'id' => $report->id]), 'data-target' => '#status-change-modal-body']) ?>
        <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-tag']) . ' ' . ($report->highlighted ? Yii::t('report', 'control.unhighlight') : Yii::t('report', 'control.highlight')), Url::to(['report/highlight', 'id' => $report->id]), ['class' => 'btn ' . ($report->highlighted ? 'btn-primary' : 'btn-warning')]) ?>
    <?php endif ?>

    <?php if ($report->status != Report::STATUS_DELETED): ?>
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)): ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']) . ' ' . Yii::t('report', 'control.edit'), ['report/update', 'id' => $report->id], ['class' => 'btn btn-primary btn-edit', 'data-report-id' => $report->id]) ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-envelope']) . ' ' . Yii::t('report', 'control.send'), ['report/send', 'id' => $report->id], ['class' => 'btn btn-primary btn-send-report btn-modal-content', 'data-modal' => '#send-modal', 'data-url' => Url::to(['report/send', 'id' => $report->id]), 'data-target' => '#send-modal-body']) ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-cloud-upload']) . ' ' . Yii::t('report', 'control.answer'), ['report/answer', 'id' => $report->id], ['class' => 'btn btn-primary btn-upload-answer btn-modal-content', 'data-modal' => '#answer-modal', 'data-url' => Url::to(['report/answer', 'id' => $report->id]), 'data-target' => '#answer-modal-body']) ?>
        <?php endif ?>

        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_DELETE)): ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']) . ' ' . Yii::t('report', 'control.delete'), ['report/delete', 'id' => $report->id], ['class' => 'btn btn-danger btn-modal-content btn-delete', 'data-url' => Url::to(['report/delete', 'id' => $report->id]), 'data-target' => '#report-delete-modal-body', 'data-modal' => '#report-delete-modal']) ?>
        <?php endif ?>
    <?php endif ?>
</div>
