<?php

use yii\helpers\Html;
use app\components\helpers\Link;
use \yii\helpers\Url;

?>
<aside class="hero__dual">
    <div class="hero__container">
        <div class="hero__dualbox hero__dualbox--support text-center">
            <h2 class="heading heading--2 hero__title"><?= Yii::t('app', 'hero-bottom-dual.support.title'); ?></h2>
            <p class="hero__lead">
                <?= Yii::t('app', 'hero-bottom-dual.support.lead'); ?>
            </p>

            <div class="hero__button">
                <?= Html::a(Yii::t('app', 'hero.support'), Url::to(['/about/support']), ['class' => 'button button--large button--primary']); ?>
            </div>
        </div>
        <div class="hero__dualbox hero__dualbox--report">
            <h2 class="heading heading--2 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
            <p class="hero__lead">
                <?= Yii::t('app', 'hero.lead'); ?>
            </p>

            <div class="hero__button">
                <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
            </div>
        </div>
    </div>
</aside>
