<?php
use app\components\helpers\Link;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<aside class="hero hero--default hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'about.title'); ?></h2>
                    <p class="hero__lead">
                        <?= Yii::t('about', 'about.lead'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="container form--padding">
    <div class="row center-xs">
        <div class="col-xs-12 col-lg-9 text-left">
            <div>
                <h1 class="heading heading--3"><?= Yii::t('label', 'footer.how_it_works') ?></h1>
                <hr />
                <p>
                    <?= Yii::t('about', 'section.howitworks.lead') ?>
                    <?= Html::a(Yii::t('menu', 'get_to_know_us'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]), ['class' => 'link link--info']) ?>
                </p>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div>
                        <h2><?= Yii::t('meta', 'title.contact') ?></h2>
                        <hr />
                        <p><?= Yii::t('label', 'footer.write_to_us.1') ?></p>
                        <p><?= Html::a(Yii::t('label', 'footer.write_to_us.0'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_CONTACT]), ['class' => 'link link--info']) ?></p>
                    </div>
                    <div>
                        <h2><?= Yii::t('label', 'footer.know_more.team') ?></h2>
                        <hr />
                        <p><?= Yii::t('about', 'team.lead1') ?></p>
                        <p><?= Html::a(Yii::t('about', 'team.title'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TEAM]), ['class' => 'link link--info']) ?></p>
                    </div>
                    <div>
                        <h2><?= Yii::t('meta', 'title.annual_reports') ?></h2>
                        <hr />
                        <p><?= Yii::t('about', 'section.annual_reports.lead') ?></p>
                        <p><?= Html::a(Yii::t('about', 'section.annual_reports.link'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_ANNUALREPORTS]), ['class' => 'link link--info']) ?></p>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div>
                        <h2><?= Yii::t('label', 'footer.know_more.statistics') ?></h2>
                        <hr />
                        <p><?= Yii::t('about', 'section.statistics.lead') ?></p>
                        <p><?= Html::a(Yii::t('about', 'section.statistics.link'), Link::to([Link::STATISTICS]), ['class' => 'link link--info']) ?></p>
                    </div>
                    <div>
                        <h2><?= Yii::t('meta', 'title.partners') ?></h2>
                        <hr />
                        <p><?= Yii::t('about', 'section.partners.lead') ?></p>
                        <p><?= Html::a(Yii::t('about', 'section.partners.link'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_PARTNERS]), ['class' => 'link link--info']) ?></p>
                    </div>
                    <div>
                        <h2><?= Yii::t('meta', 'title.tos') ?></h2>
                        <hr />
                        <p><?= Yii::t('about', 'section.tos.lead') ?>
                        <p><?= Html::a(Yii::t('about', 'section.tos.link'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TOS]), ['class' => 'link link--info']) ?></p>
                    </div>
                </div>
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
