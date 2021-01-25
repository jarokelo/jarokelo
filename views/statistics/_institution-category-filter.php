<?php

use app\components\helpers\Link;
use yii\helpers\Html;
use app\components\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\forms\StatInstitutionCategoryForm */
/* @var $form app\components\ActiveForm */

$bundle = \app\assets\AppAsset::register($this);
$dropDownWrapperOptions = ['class' => 'select select--primary'];

$form = ActiveForm::begin([
    'id' => 'statistics-institution-category-filter-form',
    'action' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_INSTITUTIONS]),
    'enableClientValidation' => false,
    'method' => 'get',
    'options' => [
        'data-pjax' => 1,
        'class' => 'middle init-loader',
    ],
    'fieldConfig' => [
        'template' => '{input}',
        'options' => [
            'class' => 'inline mobile__block',
        ],
    ],
]); ?>

<div class="statistics__subfilter">
    <div class="filter__title filter__title--after filter__title--before filter__title--inline">
        <?= $model2->institution_id ? 'Illetékeshez tartozó' : 'Összes illetékeshez tartozó' ?>
    </div>
    <?= $form->field($model, 'limit')->dropDownList([10 => 'Top 10', 15 => 'Top 15', 20 => 'Top 20'], ['class' => ''], $dropDownWrapperOptions) ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline">
        kategória
    </div>
    <?= Html::submitButton('submit', ['style' => 'display:none;'])?>
</div>

<?php ActiveForm::end();
