<?php

/** @var $bundle \app\assets\AppAsset */

use yii\helpers\Url;
use app\components\helpers\Link;
use app\components\helpers\SVG;
?>

<section class="hero hero--annual-reports">
    <div class="container">
        <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'annual_reports.title'); ?></h2>
        <p class="hero__lead col--centered">
            <?= Yii::t('about', 'annual_reports.lead'); ?>
        </p>
    </div>
</section>

<section class="container reports-block">

    <div class="row center-lg">
        <div class="col-xs-12 col-lg-9">
            <div class="row">

                <article class="reports-block__item col-xs-12 col-lg-4">
                    <h3 class="heading heading--4 heading--btm-bordered">Beszámolók</h3>
                    <a class="link link--default" href="">
                        <?= SVG::icon(SVG::ICON_DOCUMENT, ['class' => 'icon icon--largest'])?>Beszámoló
                    </a>
                </article>

                <article class="reports-block__item col-xs-12 col-lg-4">
                    <h3 class="heading heading--4 heading--btm-bordered">Alapító iratok</h3>

                    <ul>
                        <li>
                            <a class="link link--default" href="">
                                <?= SVG::icon(SVG::ICON_DOCUMENT, ['class' => 'icon icon--largest'])?>
                                    Alapító okirat
                            </a>
                        </li>
                    </ul>
                </article>

                <article class="reports-block__item col-xs-12 col-lg-4">
                    <h3 class="heading heading--4 heading--btm-bordered">Támogatások</h3>
                    <p>Támogatta</p>
                </article>

            </div>
        </div>
    </div> <!-- end .row -->

</section> <!-- end .reports-block -->

<?= $this->render('/_snippets/_hero-bottom-dual'); ?>
