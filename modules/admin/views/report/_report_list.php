<?php

use app\models\db\Report;
use app\models\db\Rule;

use app\modules\admin\models\ReportSearch;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\widgets\ListView;
use app\components\widgets\Pjax;

/* @var \yii\web\View $this */
/* @var string $action */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var boolean $disableInstitution */

if (!isset($disableInstitution)) {
    $disableInstitution = false;
}

?>
<?php $form = ActiveForm::begin([
    'id' => 'report-grid-view-search',
    'enableClientValidation' => false,
    'action' => $action,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'change-pjax-submit',
        'data-pjax-selector' => '#report-grid',
    ],
]) ?>

<div class="row block--grey">
    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'text')->textInput([
            'autocomplete' => 'off',
        ]) ?>
    </div>
    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'status')->widget(Select2::className(), [
            'data' => ['' => Yii::t('report', 'search.all_statuses')] + Report::adminFilteredStatuses(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>
    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'category')->widget(Select2::className(), [
            'data' => ['' => Yii::t('report', 'search.all_categories')] + \app\models\db\ReportCategory::getList(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>
    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'city')->widget(Select2::className(), [
            'data' => ['' => Yii::t('report', 'search.all_cities')] + $searchModel->getAvailableCities(),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'disabled' => $disableInstitution,
            ],
        ]) ?>
    </div>
    <?php Pjax::begin([
        'id' => 'report-search-institution',
    ]) ?>

    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'institution')->widget(Select2::className(), [
            'data' => ['' => Yii::t('report', 'search.all_institution')] + $searchModel->getAvailableInstitutions(),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'disabled' => $disableInstitution,
            ],
        ]) ?>
    </div>

    <div class="col-md-2 col-sm-12">
        <?= $form->field($searchModel, 'highlighted')->widget(Select2::className(), [
            'data' => ['' => Yii::t('report', 'search.all_highlighted'), 0 => Yii::t('yii', 'No'), 1 => Yii::t('yii', 'Yes')],
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>

    <?php Pjax::end() ?>

</div>

<div class="row">
    <div class="col-md-8 col-sm-12"><?= $form->field($searchModel, 'sort')->dropDownList(ReportSearch::getSortData()) ?></div>
</div>
<?php ActiveForm::end() ?>

<div>
    <?php Pjax::begin([
        'id' => 'report-grid',
        'formSelector' => '#report-grid-view-search',
        'options' => [
            'class' => 'pjax-reload-other-pjax',
            'data-reload-pjax-selector' => '#report-search-institution',
        ],
    ]) ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_report_block',
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        'layout' => "{summary}\n{items}\n<div class=\"text-center\">{pager}</div>",
    ]) ?>

    <?php Pjax::end() ?>
</div>
