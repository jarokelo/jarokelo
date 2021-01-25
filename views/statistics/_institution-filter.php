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
/* @var $model app\models\forms\StatInstitutionForm */
/* @var $form app\components\ActiveForm */

$dropDownWrapperOptions = ['class' => 'select select--primary select--primary--long'];

$bundle = \app\assets\AppAsset::register($this);
$form = ActiveForm::begin([
    'id' => 'statistics-institution-filter-form',
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
            'class' => 'inline',
        ],
    ],
]); ?>

<div class="statistics__subfilter">
    <?= $form->field($model, 'institution_id')->dropDownList(\app\models\db\Institution::getList(), ['class' => '', 'prompt' => Yii::t('label', 'generic.all_institution')], $dropDownWrapperOptions) ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline">

    </div>
<?= $form->field($model, 'days')->dropDownList([30 => 'az elmúlt 30 napban', 60 => 'az elmúlt 60 napban', 90 => 'az elmúlt 90 napban'], ['class' => ''], $dropDownWrapperOptions) ?>
</div>

<?= Html::submitButton('submit', ['style' => 'display:none;'])?>
<?php ActiveForm::end();
