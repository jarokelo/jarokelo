<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\helpers\Link;

?><aside class="support-box">
    <div class="container">
        <div class="row">
            <div class="support-box__content col-xs-12 col-md-7">
                <h2 class="heading heading--3 support-box__title"><?= Yii::t('app', 'hero-bottom-dual.support.title'); ?></h2>
                <p class="support-box__lead">
                    <?= Yii::t('app', 'hero-bottom-dual.support.lead'); ?>
                </p>

                <div class="support-box__button">
                    <?= Html::a(Yii::t('app', 'hero.support'), Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_SUPPORT]), ['class' => 'button button--large button--primary']); ?>
                </div>
            </div>
            <div class="support-box__media col-xs-12 col-md-5">
                <div class="video-wrapper">
                  <iframe class="support-box_video" frameborder="0" height="100%" width="100%" src="https://www.youtube.com/embed/qKvP8GI6e2g?showinfo=0">
                  </iframe>
                </div>
            </div>
        </div>
    </div>
</aside>
