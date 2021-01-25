<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\Report;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\ActiveForm;
use app\models\db\City;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\forms\StatisticsCityForm */
/* @var $form app\components\ActiveForm */

$bundle = \app\assets\AppAsset::register($this);
$dropDownWrapperOptions = ['class' => 'select select--primary'];

$form = ActiveForm::begin([
    'id' => 'statistics-city-filter-form',
    'action' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES, Yii::$app->request->get('citySlug')]),
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

<div class="statistics__mainfilter">
    <?= $form->field($model, 'city_id')->dropDownList(City::availableCities(true, false), ['class' => '', 'prompt' => Yii::t('label', 'generic.all_city')], $dropDownWrapperOptions) ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline">statisztikája</div>
    <?= $form->field($model, 'days')->dropDownList([30 => 'az elmúlt 30 napban', 60 => 'az elmúlt 60 napban', 90 => 'az elmúlt 90 napban'], ['class' => ''], $dropDownWrapperOptions) ?>
</div>
<div class="statistics__subfilter">
    <?= $form->field($model, 'limit')->dropDownList([10 => 'Top 10', 15 => 'Top 15', 20 => 'Top 30'], ['class' => ''], $dropDownWrapperOptions) ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline">illetékes a bejelentések száma szerint</div>
</div>

<?= Html::submitButton('submit', ['style' => 'display:none;'])?>
<?php ActiveForm::end();
