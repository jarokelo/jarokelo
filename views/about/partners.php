<?php

use yii\helpers\Html;
use app\assets\AppAsset;

$assetUrl = AppAsset::register($this)->baseUrl;

?>

<aside class="hero hero--default">
    <div class="container">
        <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'partners.title'); ?></h2>
        <p class="hero__lead col-8 col--centered">
            <?= Yii::t('about', 'partners.lead'); ?>
        </p>
    </div>
</aside>

<div class="partners container container--box center--images">
    <div class="row mb-35">
        <div class="col-xs-12">
            <h1 class="heading heading--2">Partnereink</h1>
            <div class="row middle-xs center-xs">
                <div class="col-xs-12 col-md-6 col-lg-3">
                    <div class="">
                        Partner
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h1 class="heading heading--2">Támogatóink</h1>
            <div class="row middle-xs center-xs">
                <div class="col-xs-12 col-md-6 col-lg-3">
                    <div class="">
                        Támogató
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('/_snippets/_hero-bottom-dual'); ?>
