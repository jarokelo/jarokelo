<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\Institution;
use yii\helpers\Html;
use app\components\ActiveForm;
use app\models\db\City;
use app\models\db\Report;
use app\models\db\District;
use app\models\db\ReportCategory;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\db\search\ReportSearch */
/* @var $form app\components\ActiveForm */

$bundle = \app\assets\AppAsset::register($this);
$dropDownWrapperOptions = ['class' => 'select select--primary hidden--mobile'];
$secondFilterFieldOptions = [
    'template' => '{label}{input}',
    'labelOptions' => [
        'class' => 'label label--default',
    ],
];
$secondFilterDropDownWrapperOptions = ['class' => 'select select--small select--full'];

?>

<?php $form = ActiveForm::begin([
    'action' => Link::to($type),
    'id' => 'report-search-form',
    'enableClientValidation' => false,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'init-loader',
    ],
    'fieldConfig' => [
        'template' => '{input}',
        'options' => [
            'class' => 'inline',
        ],
    ],
]); ?>
<div class="report-search-filter">
    <div class="filter filter--subpage">

        <ul class="filter__icons list list--horizontal">
            <li>
                <?= Html::a(SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'link__icon icon icon--before']) . Yii::t('report', 'search.detailed'), '#', ['class' => 'report-list--filter link link--default']); ?>
            </li>
            <li>
                <?php
                $icon = $type == Link::REPORTS ? SVG::ICON_MAP : SVG::ICON_DOCUMENTS;
                $link = Link::convertTo($type == Link::REPORTS ? Link::MAP : Link::REPORTS);
                $text = $type == Link::REPORTS ? Yii::t('report', 'search.map') : Yii::t('report', 'search.list')
                ?>
                <?= Html::a(SVG::icon($icon, ['class' => 'link__icon icon icon--before']) . $text, $link, ['class' => 'link link--default']) ?>
            </li>
        </ul>

        <?= $form->field($model, 'highlighted')->hiddenInput() ?>
        <?= $form->field($model, 'status')->dropDownList(Report::statusFilterItems(), ['class' => '', 'prompt' => Yii::t('label', 'generic.all')], $dropDownWrapperOptions) ?>
        <div class="filter__title filter__title--after filter__title--before filter__title--inline hidden--mobile--inline-block">
            <?= $model->status !== null ? Yii::t('report', 'filter.label-after-type-select') : Yii::t('report', 'filter.label-after-type-select-null') ?>
        </div>
        <?= $form->field($model, 'city_id')->dropDownList(City::getAllForFilter(true, false), ['class' => ''], $dropDownWrapperOptions) ?>
        <div class="filter__title filter__title--after filter__title--before filter__title--inline hidden--mobile--inline-block">
            <?= \yii\helpers\ArrayHelper::getValue($model, 'district.article') ?>
        </div>
        <?php if (\yii\helpers\ArrayHelper::getValue($model, 'city.has_districts', false)): ?>
        <?= $form->field($model, 'district_id')->dropDownList($model->city_id ? District::getAll($model->city_id, 'name_filter') : [], ['class' => '', 'prompt' => Yii::t('label', 'generic.all_districts')], $dropDownWrapperOptions) ?>
        <?php endif; ?>
    </div>
</div>

<div class="section">
    <div id="front-report-search" class="report-search" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
        <a class="close init-loader" href="<?= Url::to(Yii::$app->request->getPathInfo(), true) ?>" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
            <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'link__icon icon icon--large'])?>
        </a>

        <div class="row">
            <div class="form__row col-xs-12 col-md-4 hidden--desktop">
                <?= $form->field($model, 'status', $secondFilterFieldOptions)
                    ->dropDownList(Report::statusFilterItems(), [
                        'class' => '',
                        'id' => 'reportsearch-status-mobile',
                        'prompt' => Yii::t('label', 'generic.all'),
                    ], $secondFilterDropDownWrapperOptions) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4 hidden--desktop">
                <?= $form->field($model, 'city_id', $secondFilterFieldOptions)
                    ->dropDownList(City::availableCities(true, false), [
                        'class' => '',
                        'id' => 'reportsearch-city_id-mobile',
                        'prompt' => Yii::t('label', 'generic.all'),
                    ], $secondFilterDropDownWrapperOptions) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4 hidden--desktop">
                <?= $form->field($model, 'district_id', $secondFilterFieldOptions)
                    ->dropDownList($model->city_id ? District::getAll($model->city_id) : [], [
                        'class' => '',
                        'id' => 'reportsearch-district_id-mobile',
                        'prompt' => Yii::t('label', 'generic.all'),
                    ], $secondFilterDropDownWrapperOptions) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4">
                <?= $form->field($model, 'name', $secondFilterFieldOptions)
                    ->textInput(['onchange' => 'this.form.submit()'])->label(Yii::t('report', 'search.name')) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4">
                <?= $form->field($model, 'report_category_id', $secondFilterFieldOptions)
                    ->dropDownList(ReportCategory::getList(), ['class' => '', 'prompt' => Yii::t('report', 'search.select_category')], $secondFilterDropDownWrapperOptions) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4">
                <?= $form->field($model, 'institution_id', $secondFilterFieldOptions)
                    ->dropDownList(Institution::getList(), ['class' => '', 'prompt' => Yii::t('report', 'search.select_institution')], $secondFilterDropDownWrapperOptions) ?>
            </div>
        </div>

    </div>
</div>
<?= Html::submitButton('submit', ['style' => 'display:none;'])?>
<?php ActiveForm::end(); ?>
