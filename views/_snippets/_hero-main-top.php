<?php
use app\components\helpers\Link;
use yii\helpers\Html;
use app\components\helpers\SVG;

?>
<article class="hero hero--default">
    <div class="container">
        <h2 class="heading heading--1 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
        <p class="hero__lead col-8 col--centered">
            <?= Yii::t('app', 'hero.lead'); ?>
        </p>

        <div class="hero__button">
            <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']) ?>
        </div>
        <?= Html::a(SVG::icon(SVG::ICON_MAP, ['fill' => '#fff']) . ' ' . Yii::t('app', 'hero.know_more'), Link::to(Link::MAP), ['class' => 'hero__link']) ?>
    </div>
</article>
