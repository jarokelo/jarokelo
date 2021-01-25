<?php
/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

use app\models\db\Report;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\components\widgets\Pjax;

$gridColumns = [
    [
        'attribute' => 'reportId',
        'label' => 'Bejelentés azonosító',
        'value' => function ($model) {
            /** @var \app\models\db\Report $model */
            return $model['nameAndUniqueId'];
        },
    ],
    [
        'attribute' => 'user_location',
        'label' => Yii::t('user', 'report.user_location'),
    ],
    [
        'attribute' => 'latitude',
        'label' => Yii::t('user', 'report.latitude'),
    ],
    [
        'attribute' => 'longitude',
        'label' => Yii::t('user', 'report.longitude'),
    ],
    [
        'attribute' => 'statusName',
        'label' => Yii::t('user', 'report.status'),
        'value' => function ($model) {
            /** @var \app\models\db\Report $model */
            return Yii::t('const', 'report.status.' . $model['status']);
        },
    ],
    [
        'attribute' => 'reportCategoryName',
        'label' => Yii::t('user', 'report.report_category_name'),
    ],
    [
        'attribute' => 'created_at',
        'label' => Yii::t('user', 'report.created_at'),
        'format' => 'raw',
        'value' => function ($model) {
            /* @var \app\models\db\User $user */
            return date('Y-m-d H:i:s', $model['created_at']);
        },
    ],
    [
        'attribute' => 'institutionName',
        'label' => Yii::t('user', 'report.institution_name'),
    ],
    [
        'attribute' => 'description',
        'label' => Yii::t('report', 'description'),
    ],
    [
        'attribute' => 'url',
        'label' => Yii::t('user', 'report.url'),
        'value' => function ($model) {
            /* @var \app\models\db\User $user */
            return Url::to(['/' . $model['id']], true);
        },
    ],
    [
        'attribute' => 'project',
        'label' => Yii::t('data', 'report.inclusion.project'),
        'value' => function (array $model) {
            $label = Yii::t('data', 'report.project.default');

            if (empty($model['project'])) {
                return $label;
            }

            if (isset(Report::$projects[$model['project']])) {
                $message = 'report.' . strtolower(Report::$projects[$model['project']]) . '.label';
                $label = Yii::t('data', $message);
            }

            return $label;
        },
    ],
];
?>

<div class="row block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'report-grid-view-search',
        'enableClientValidation' => false,
        'action' => ['report/statistics', 'tab' => \app\modules\admin\controllers\ReportController::TAB_REPORTS],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'change-pjax-submit',
            'data-pjax-selector' => '#report-search',
        ],
    ]) ?>
    <div class="col-md-6">
        <?= $form->field($searchModel, 'start_date')->widget(\kartik\date\DatePicker::className(), [
            'pluginOptions' => ['format' => 'yyyy-mm-dd'],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($searchModel, 'end_date')->widget(\kartik\date\DatePicker::className(), [
            'pluginOptions' => ['format' => 'yyyy-mm-dd'],
        ]) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?php Pjax::begin([
    'id' => 'report-search',
    'formSelector' => '#report-grid-view-search',
    'options' => [
        'data-pjax-target' => 'report-grid-view-search',
    ],
]) ?>

<?=\kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'type' => 'default',
        'after' => false,
        'showFooter' => false,
    ],
    'toolbar' => ['{export}'],
    'floatHeader' => true,
    'responsive' => false,
    'exportConfig' => [
        \kartik\grid\GridView::CSV => [
            'label' => 'CSV',
            'icon' => 'floppy-open',
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'colDelimiter' => ',',
            'rowDelimiter' => "\r\n",
            'filename' => 'grid-export',
            'alertMsg' => 'The CSV export file will be generated for download.',
            'options' => ['title' => 'Mentés CSV-ként'],
        ],
        \kartik\grid\GridView::EXCEL => [
            'label' => 'Excel',
            'icon' => 'floppy-open',
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'colDelimiter' => ',',
            'rowDelimiter' => "\r\n",
            'filename' => 'grid-export',
            'alertMsg' => 'The Excel export file will be generated for download.',
            'options' => ['title' => 'Mentés XLS-ként'],
        ],
    ],
    'filterModel' => false,
    'filterPosition' => false,
    'tableOptions' => [
        'class' => 'hidden',
    ],
    'columns' => $gridColumns,
    'export' => [
        'target' => \kartik\grid\GridView::TARGET_SELF,
        'label' => Yii::t('report', 'export.list'),
    ],
]);

Pjax::end();
