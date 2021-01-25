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
$dropDownWrapperOptions = ['class' => 'select select--primary hidden--mobile'];

$form = ActiveForm::begin([
    'id' => 'statistics-city-category-filter-form',
    'action' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES, Yii::$app->request->get('citySlug')]),
    'enableClientValidation' => false,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'middle init-loader',
    ],
    'fieldConfig' => [
        'template' => '{input}',
        'options' => [
            'class' => 'inline',
        ],
    ],
]); ?>

<div class="statistics__subfilter">
    <?= $form->field($model, 'limit')->dropDownList([10 => 'Top 10', 15 => 'Top 15', 20 => 'Top 20'], ['class' => ''], $dropDownWrapperOptions) ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline hidden--mobile--inline-block">kategória a bejelentések száma szerint</div>
</div>
<?= Html::submitButton('submit', ['style' => 'display:none;'])?>
<?php ActiveForm::end();
