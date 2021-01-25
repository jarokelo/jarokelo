<?php

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use yii\web\View;
use \yii\helpers\Url;

/* @var \app\models\db\ReportActivity $model */

$formatJs = <<< 'JS'
var formatReport = function (report) {
    if (report.loading) {
        return report.nameAndUniqueId;
    }
    
    var markup =
'<div class="row">' + 
    '<div class="col-sm-12">' + report.nameAndUniqueId + '</div>' +
'</div>';
    return '<div style="overflow:hidden;">' + markup + '</div>';
};
var formatReportSelection = function (report) {
    return report.nameAndUniqueId;
}
JS;

// Register the formatting script
$this->registerJs($formatJs, View::POS_HEAD);

$dataExp = <<< JS
  function(params) {
    return {
        q: params.term,
        page: params.page,
    };
}
JS;

$resultsJs = <<< JS
function (data, params) {
    params.page = params.page || 1;
    return {
        results: data.results,
        pagination: {
            more: data.pagination.more
        }
    };
}
JS;
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'assign-report-ajax',
        'action' => ['task/assign', 'id' => $model->id],
        'enableClientValidation' => true,
    ]) ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('task', 'assign.title') ?></h4>
    </div>

    <div class="modal-body">
        <?= Html::activeHiddenInput($model, 'is_active_task') ?>
        <p>
            <?= Yii::t('task', 'assign.description', [
                'from' => $model->institution === null ? ($model->email === null ? 'unknown' : $model->email->from) : $model->institution->name,
                'to'   => $model->email === null ? 'unknown' : $model->email->to,
            ]) ?>
        </p>
        <div class="row">
                <div class="col-md-9">
                    <?= $form->field($model, 'report_id')
                        ->widget(Select2::className(), [
                            'theme' => Select2::THEME_KRAJEE,
                            'options' => [
                                'placeholder' => Yii::t('task', 'assign.placeholder'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 2,
                                'ajax' => [
                                    'url' => Url::toRoute(['/admin/report/list-report']),
                                    'dataType' => 'json',
                                    'delay' => 250,
                                    'data' => new JsExpression($dataExp),
                                    'processResults' => new JsExpression($resultsJs),
                                    'cache' => true,
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('formatReport'),
                                'templateSelection' => new JsExpression('formatReportSelection'),
                            ],
                        ])
                        ->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::submitButton(Yii::t('button', 'assign'), ['class' => 'btn btn-primary']) ?>
                </div>
        </div>
        <?php if ($model->email !== null): ?>
            <div><b><?= Yii::t('task', 'assign.mail.subject') ?></b></div>
            <div><?= $model->email->subject ?></div><br />

            <div><b><?= Yii::t('task', 'assign.mail.content') ?></b></div>
            <div><?= \app\components\helpers\Html::formatText($model->email->body) ?></div><br />

            <?php if (count($model->email->reportAttachments) > 0): ?>
                <div><b><?= Yii::t('task', 'assign.mail.attachment') ?></b></div>
                <?php foreach ($model->email->reportAttachments as $attachment): ?>
                    <div><?= $attachment->name ?> <?=Html::a('', $attachment->getAttachmentUrl(), ['target' => '_blank', 'class' => 'btn-primary btn-sm glyphicon glyphicon-share']); ?></div>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
