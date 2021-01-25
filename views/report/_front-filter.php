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
/* @var $model app\models\db\search\ReportSearch */
/* @var $form app\components\ActiveForm */

$bundle = \app\assets\AppAsset::register($this);
$dropDownWrapperOptions = ['class' => 'select select--primary'];

?>

<ul class="filter__links list list--horizontal hidden--mobile">
    <li>
        <a href="<?= Link::to(Link::REPORTS); ?>" class="link link--default">
            <?= SVG::icon(SVG::ICON_DOCUMENTS, ['class' => 'link__icon icon icon--before'])?>
            <?= Yii::t('label', 'generic.all_reports'); ?>
        </a>
    </li>
    <li>
        <a href="#" class="link link--default front-filter--search-icon-link">
            <?= SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'link__icon icon icon--before'])?>
            <?=Yii::t('label', 'generic.search'); ?>
        </a>
    </li>
</ul>

<div class="filter__title">
    <?= Yii::t('label', 'report.resolved_issue', [
        'number' => Report::countResolved($model->city_id),
    ]); ?>
</div>

<?php $form = ActiveForm::begin([
    'action' => Link::to(Link::HOME),
    'id' => 'front-search-form',
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


<?= $form->field($model, 'city_id')->dropDownList(City::getAllForFilter(true), ['class' => '', 'prompt' => Yii::t('label', 'generic.all_city')], $dropDownWrapperOptions) ?>

<?= Html::submitButton('submit', ['style' => 'display:none;'])?>
<?php ActiveForm::end(); ?>

<div class="row hidden--desktop center-xs offset--top">
    <div class="col-xs-9 col-md-7">
        <a href="<?= Link::to(Link::REPORTS); ?>" class="link link--default offset--right">
            <?= SVG::icon(SVG::ICON_DOCUMENTS, ['class' => 'link__icon icon icon--before'])?>
            <?= Yii::t('label', 'generic.all_reports'); ?>
        </a>
        <a href="#" class="link link--default front-filter--search-icon-link offset--left">
            <?= SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'link__icon icon icon--before'])?>
            <?=Yii::t('label', 'generic.search'); ?>
        </a>
    </div>
</div>
