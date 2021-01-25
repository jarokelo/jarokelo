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
use yii\jui\DatePicker;

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

<!--<script src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>-->

<div class="report-search-filter map-filter">
    <div class="filter filter--subpage  hidden--desktop">
        <ul class="filter__icons list list--horizontal">
            <li>
                <?= Html::a(SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'link__icon icon icon--before']) . Yii::t('report', 'search.detailed'), '#', ['class' => 'report-list--filter link link--default']); ?>
            </li>
        </ul>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'action' => Link::to($type),
    'id' => 'report-search-form',
    'enableClientValidation' => false,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'change-pjax-submit',
        'data-pjax-selector' => '#district-search',
    ],
]); ?>
<div class="section hidden--desktop">
    <div id="front-report-search" class="report-search" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
        <a class="close init-loader" href="<?= Url::to(Yii::$app->request->getPathInfo(), true) ?>" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
            <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'link__icon icon icon--large'])?>
        </a>
        <div class="row">
            <div class="form__row col-xs-12 col-md-4">
                <?= $form->field($model, 'city_id', $secondFilterFieldOptions)
                    ->dropDownList(City::availableCities(true, false), [
                        'class' => '',
                        'id' => 'reportsearch-city_id-mobile',
                        'prompt' => Yii::t('label', 'generic.all'),
                    ], $secondFilterDropDownWrapperOptions) ?>
            </div>
            <div class="form__row col-xs-12 col-md-4">
                <div class="location-container">
                    <?= $form->field($model, 'location', $secondFilterFieldOptions)
                        ->textInput(
                            [
                                'class' => 'input input--default map-search-form-location',
                                'placeholder' => Yii::t('report', 'search.address_placeholder'),
                                'id' => 'map-search-form-location-mobile',
                            ]
                        )
                    ?>
                    <button type="button" class="button button--green button-show_me-mobile button--solid button--round-icon hidden--desktop" show-me-on-map>
                        <?= SVG::icon(SVG::ICON_TARGET, ['class' => 'icon'])?>
                    </button>
                    <button type="button" class="button button-show_me-mobile--clear">
                        <?= SVG::icon(SVG::ICON_CLOSE_WHITE, ['class' => 'icon'])?>
                    </button>
                </div>
            </div>
            <div class="form__row col-xs-12 col-md-4">
                <?= $form->field($model, 'status', $secondFilterFieldOptions)
                    ->dropDownList(Report::statusFilterItems(), [
                        'class' => '',
                        'id' => 'reportsearch-status-mobile',
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

<?php ActiveForm::end(); ?>

<?php $form = ActiveForm::begin([
    'action' => Link::to($type),
    'id' => 'report-map-search-form',
    'enableClientValidation' => false,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'change-pjax-submit',
        'data-pjax-selector' => '#district-search',
    ],
]); ?>
<div class="section map-search-form-section hidden--mobile">
    <div id="front-report-search" class="report-search">
        <?= Html::a(Yii::t('report', 'button.reset_filter'), [Yii::$app->request->getPathInfo()], ['class' => 'link link--info reset-link']) ?>
        <div class="row map-search-form-row">
            <div class="col-xs-12 col-md-4">
                <div>
                    <?= $form->field($model, 'report_category_id', $secondFilterFieldOptions)
                        ->dropDownList(ReportCategory::getList(), ['class' => '', 'prompt' => Yii::t('report', 'search.select_category')], $secondFilterDropDownWrapperOptions)
                    ?>
                </div>
                <div class="map-search-form-row">
                    <?= $form->field($model, 'institution_id')
                        ->dropDownList(Institution::getList(), ['class' => '', 'prompt' => Yii::t('report', 'search.select_institution')], $secondFilterDropDownWrapperOptions)
                    ?>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div>
                    <?= $form->field($model, 'name', $secondFilterFieldOptions)
                        ->textInput()
                        ->input('text', ['placeholder' => Yii::t('report', 'search.name')])
                    ?>
                </div>
                <div class="map-search-form-row form__row">
                    <div class="field-reportsearch-date_from form__row">
                        <label class="label label--default" for="reportsearch-date_from">
                            <?= \Yii::t('report', 'search.period') ?>
                        </label>
                        <div class="row">
                            <div class="col-xs-6 col-md">
                                <?= DatePicker::widget([
                                    'name' => 'date_from',
                                    'model' => $model,
                                    'attribute' => 'date_from',
                                    'options' => ['placeholder' => Yii::t('report', 'search.date_from')],
                                    'dateFormat' => 'php:Y-m-d',
                                    'clientOptions' => [
                                        'maxDate' => date('Y-m-d'),
                                        'onSelect' => new \yii\web\JsExpression(
                                            'function(date) {
                                                if ($(\'#reportsearch-date_to\').val() !== null) {
                                                    var selectedDate = new Date(date);
                                                    var endDate = new Date(new Date(date).valueOf() + 30 * 24 * 60 * 60 * 1000);
                                                    $(\'#reportsearch-date_to\').datepicker(\'option\', \'minDate\', selectedDate);
                                                    if (endDate < new Date()) {
                                                        $(\'#reportsearch-date_to\').datepicker(\'option\', \'maxDate\', endDate);
                                                }
                                                $(\'#reportsearch-date_from\').datepicker(\'option\', \'minDate\', null);
                                            }
                                        }'
                                        ),
                                    ],
                                ]) ?>
                            </div>
                            <div class="col-xs-6 col-md">
                                <?= DatePicker::widget([
                                    'name' => 'date_to',
                                    'model' => $model,
                                    'attribute' => 'date_to',
                                    'options' => ['placeholder' => Yii::t('report', 'search.date_to')],
                                    'dateFormat' => 'php:Y-m-d',
                                    'clientOptions' => [
                                        'maxDate' => date('Y-m-d'),
                                        'onSelect' => new \yii\web\JsExpression('
                                        function(date) {
                                            if ($(\'#reportsearch-date_from\').val() !== null) {
                                                var selectedDate = new Date(date);
                                                var startDate = new Date(new Date(date).valueOf() - 30 * 24 * 60 * 60 * 1000);
                                                $(\'#reportsearch-date_from\').datepicker(\'option\', \'minDate\', startDate);
                                                $(\'#reportsearch-date_from\').datepicker(\'option\', \'maxDate\', selectedDate);
                                            }
                                            $(\'#reportsearch-date_to\').datepicker(\'option\', \'minDate\', null);
                                        }'),
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <label class="label--default map-search-help-text" for="reportsearch-date_from">
                        <?= Yii::t('report', 'search.text.date') ?>
                    </label>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div>
                    <?= $form->field($model, 'city_id')
                        ->dropDownList(City::availableCities(true, false), ['class' => '', 'prompt' => Yii::t('report', 'search.select_city'), 'id' => 'report-map-search-form-city'], $secondFilterDropDownWrapperOptions)
                    ?>
                </div>
                <div class="map-search-form-row form__row">
                    <div>
                        <?= $form->field($model, 'location', $secondFilterFieldOptions)
                            ->textInput(['class' => 'input input--default step__final--hidden map-search-form-location', 'id' => 'map-search-form-location'])
                        ?>
                    </div>
                    <label class="label--default map-search-help-text" for="reportsearch-date_from">
                        <?= Yii::t('report', 'search.text.location') ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="row map-search-form-row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'waiting_for_answer')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.status.3') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'resolved')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.status.5') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'waiting_for_solution')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.status.8') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'highlighted')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.featured') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row map-search-form-row text-align-items--center">
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'waiting_for_response')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.status.4') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box">
                            <?= $form->field($model, 'unresolved')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.status.6') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box" style="display: <?= Yii::$app->user->isGuest ? 'none' : 'block' ?>;">
                            <?= $form->field($model, 'followed')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero" style="display: <?= Yii::$app->user->isGuest ? 'none' : 'block' ?>;">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.followed') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md row text-align-items--center">
                        <div class="text-left map-search-checkbox__box" style="display: <?= Yii::$app->user->isGuest ? 'none' : 'block' ?>;">
                            <?= $form->field($model, 'users_reports')
                                ->checkbox() ?>
                        </div>
                        <div class="col-md-8 margin-padding--zero" style="display: <?= Yii::$app->user->isGuest ? 'none' : 'block' ?>;">
                            <label class="map-search-checkbox__label">
                                <?= Yii::t('report', 'search.users_reports') ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 hidden--mobile">
                <div>
                    <?= Html::submitButton(Yii::t('report', 'button.show_on_map'), ['class' => 'button button--success button--solid button--full']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
