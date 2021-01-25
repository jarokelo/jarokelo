<?php

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\ReportSearch $reportSearchModel */
/* @var \app\modules\admin\models\StatisticsInstitutionSearch $institutionSearchModel */
/* @var \app\modules\admin\models\StatisticsDistrictSearch $districtSearchModel */
/* @var \yii\data\ActiveDataProvider $institutionDataProvider */
/* @var \yii\data\ActiveDataProvider $districtDataProvider */
/* @var \yii\data\ActiveDataProvider $reportStatisticsDataProvider */

use yii\bootstrap\Html;
use app\modules\admin\controllers\ReportController;
use kartik\export\ExportMenu;
use yii\bootstrap\ActiveForm;
use app\components\widgets\Pjax;

$this->title = Yii::t('menu', 'statistics');

?>
<div class="row">
    <div class="col-md-9 col-sm-12"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
    </div>
</div>

<?= \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('report', 'update.tab.institutions'),
            'content' => $tab == ReportController::TAB_INSTITUTIONS ? $this->render('_statistics-institutions', [
                'searchModel' => $institutionSearchModel,
                'dataProvider' => $institutionDataProvider,
            ]) : null,
            'active' => $tab == ReportController::TAB_INSTITUTIONS,
            'url' => ['reports/statistics', 'tab' => ReportController::TAB_INSTITUTIONS],
        ],
        [
            'label' => Yii::t('report', 'update.tab.districts'),
            'content' => $tab == ReportController::TAB_DISTRICTS ? $this->render('_statistics-districts', [
                'searchModel' => $districtSearchModel,
                'dataProvider' => $districtDataProvider,
            ]) : '',
            'active' => $tab == ReportController::TAB_DISTRICTS,
            'url' => ['reports/statistics', 'tab' => ReportController::TAB_DISTRICTS],
        ],
        [
            'label' => Yii::t('report', 'update.tab.reports'),
            'content' => $tab == ReportController::TAB_REPORTS ? $this->render('_statistics-reports', [
                'searchModel' => $reportSearchModel,
                'dataProvider' => $reportStatisticsDataProvider,
            ]) : '',
            'active' => $tab == ReportController::TAB_REPORTS,
            'url' => ['reports/statistics', 'tab' => ReportController::TAB_REPORTS],
        ],
    ],
])?>
