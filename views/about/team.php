<?php
use yii\helpers\Html;
use app\components\helpers\Link;
use yii\helpers\Url;

$asset = \app\assets\AppAsset::register($this);
?>

<aside class="hero hero--team hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-12 col-md-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'team.title'); ?></h2>
                    <p class="hero__lead hero__lead--padding">
                        <?= Yii::t('about', 'team.lead1'); ?>
                    </p>
                    <p class="hero__lead hero__lead--padding">
                        <?= Yii::t('about', 'team.lead2'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="container team">
    <h2 class="heading heading--7">Csapatunk</h2>

    <div class="row">
        <div class="col-xs-12 col-lg-6">
            <div class="team__member clearfix">
                <div class="team__media">
                    <img class="" src="<?= $asset->baseUrl; ?>/images/people.png" alt="">
                </div>
                <div class="team__text">
                    <h3 class="team__name">
                        Csapattag 1
                    </h3>
                    <p class="team__position">
                        Pozíció
                    </p>
                    <p class="team__about">
                        Leírás
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-lg-6">
            <div class="team__member clearfix">
                <div class="team__media">
                    <img class="" src="<?= $asset->baseUrl; ?>/images/people.png" alt="">
                </div>
                <div class="team__text">
                    <h3 class="team__name">
                        Csapattag 2
                    </h3>
                    <p class="team__position">
                        Pozíció
                    </p>
                    <p class="team__about">
                        Leírás
                    </p>
                </div>
            </div>
        </div>
</div>

<aside class="hero hero--bottom">
    <div class="container">
        <h2 class="heading heading--1 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
        <p class="hero__lead col-8 col--centered">
            <?= Yii::t('app', 'hero.lead'); ?>
        </p>

        <div class="hero__button">
            <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
        </div>
    </div>
</aside>
