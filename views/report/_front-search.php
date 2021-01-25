<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\helpers\Html;
use app\components\ActiveForm;
use app\models\db\City;
use app\models\db\Report;
use app\models\db\District;
use \app\assets\AppAsset;
use yii\helpers\Url;

$bundle = AppAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\db\search\ReportSearch */
/* @var $form app\components\ActiveForm */
?>

<div class="section container">
    <div id="front-report-search" class="report-search" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
        <a class="close init-loader" href="<?= Url::to(Yii::$app->request->getPathInfo(), true) ?>" style="display: <?= $model->hasFilterInSecondBlock() ? 'block;' : 'none;' ?>">
            <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'link__icon icon icon--large'])?>
        </a>
        <?= Yii::t('label', 'generic.search_in_reports'); ?>
        <?php $form = ActiveForm::begin([
            'action' => Link::to(Link::REPORTS),
            'method' => 'get',
            'options' => [
                'class' => 'init-loader',
            ],
        ]); ?>

        <?= $form->field($model, 'name')->textInput()->label(''); ?>

        <button type="submit" class="report-search-submit">
            <?= SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'link__icon icon icon--before icon--largest']) ?>
        </button>

        <?php ActiveForm::end(); ?>

    </div>
</div>
