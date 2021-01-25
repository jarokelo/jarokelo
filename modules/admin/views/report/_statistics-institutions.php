<?php
/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap\ActiveForm;
use app\components\widgets\Pjax;

?>
    <div class="row block--grey">
        <?php $form = ActiveForm::begin([
            'id' => 'institution-grid-view-search',
            'enableClientValidation' => false,
            'action' => ['report/statistics', 'tab' => \app\modules\admin\controllers\ReportController::TAB_INSTITUTIONS],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'class' => 'change-pjax-submit',
                'data-pjax-selector' => '#institution-search',
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
    'id' => 'institution-search',
    'formSelector' => '#institution-grid-view-search',
    'options' => [
        'data-pjax-target' => 'institution-grid-view-search',
    ],
]) ?>

<?= \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'heading' => false,
        'type' => 'default',
        'after' => false,
        'showFooter' => false,
    ],
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
    'columns' => [
        [
            'attribute' => 'name',
        ],
        [
            'attribute' => 'resolved',
            'label' => Yii::t('user', 'report.resolved'),
        ],
        [
            'attribute' => 'unresolved',
            'label' => Yii::t('user', 'report.unresolved'),
        ],
        [
            'attribute' => 'waiting_for_response',
            'label' => Yii::t('user', 'report.waiting_for_response'),
        ],
        [
            'attribute' => 'waiting_for_solution',
            'label' => Yii::t('user', 'report.waiting_for_solution'),
        ],
    ],
    'export' => [
        'target' => \kartik\grid\GridView::TARGET_SELF,
    ],
]);

Pjax::end();
