<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;

$assetUrl = AppAsset::register($this)->baseUrl;
// usage: echo Html::img($assetUrl.'/images/image.png');

?>

<aside class="hero hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="hidden--mobile">
            <div class="hero__background hero--how"></div>
        </div>
        <div class="hidden--desktop">
            <div class="hero__background hero--how"></div>
        </div>
        <div class="container">
            <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'howitworks.title'); ?></h2>
            <p class="hero__lead col-8 col--centered">
                <?= Yii::t('about', 'howitworks.lead'); ?>
            </p>
        </div>
    </div>
</aside>

<section class="section section--grey">
    <div class="container form--padding">
        <div class="row center-xs">
            <div class="col-xs-12 col-md-8 text-left">
                <ul class="timeline">
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_SEND, ['class' => 'icon filter__icon'])?>
                            </span>
                            Elküldöd nekünk a bejelentést
                        </h2>
                        <div class="timeline__container__box">
                            <p> A problémákat a főoldalon található <a class="link link--info" href="<?= Link::to(Link::CREATE_REPORT); ?>">Probléma bejelentése</a> gomb segítségével
                                küldheted el nekünk. Ehhez rövid címet és leírást kell adnod az esetről. Azt is jelöld meg,
                                milyen kategóriába tartozik a probléma (pl. forgalomtechnika, kátyú stb.), a térkép segítségével pedig add meg a
                                probléma helyét és tölts fel egy vagy több fényképet, vagy akár linkelhetsz videót is róla.
                            </p>
                            <p class="badge badge--new">Új</p>
                        </div>
                    </li>
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_CHECKED_DOCUMENT, ['class' => 'icon filter__icon'])?>
                            </span>
                            Ellenőrizzük a bejelentést
                        </h2>
                        <div class="timeline__container__box">
                            <p>
                                Miután elküldted a bejelentést, honlapunk adminisztrátorai ellenőrzik azt, majd a probléma felméréséhez és megoldásához szükséges információkkal együtt továbbítják az illetékeseknek.
                            </p>
                            <p class="badge badge--editing">Feldolgozás alatt</p>
                        </div>
                        <div class="timeline__container__box">
                            <p>
                                Ha esetleg további információra van szükségünk, mielőtt továbbítjuk a bejelentéset, azt is jelezni fogjuk neked.
                            </p>
                            <p class="badge badge--waiting-for-info">Kiegészítésre vár</p>
                        </div>
                    </li>
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_EMAIL_OPEN, ['class' => 'icon filter__icon'])?>
                            </span>
                            Továbbítjuk az illetékeseknek
                        </h2>
                        <div class="timeline__container__box">
                            <p>
                                Jóváhagyás után bejelentésedet még aznap továbbítjuk a probléma jellege, helyszíne és egyéb kritériumok alapján illetékes szervezetnek. A továbbküldésről automatikusan értesítünk e-mailben.
                            </p>
                            <p class="badge badge--waiting-for-answer">Válaszra vár</p>
                        </div>
                    </li>
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_CHECKED_MAIL, ['class' => 'icon filter__icon'])?>
                            </span>
                            Megérkezik az illetékes válasza
                        </h2>
                        <div class="timeline__container__box">
                            <p>
                                Miután a bejelentést elküldtük az illetékes szervezetnek, ügykezelőink figyelni fogják, hogy az illetékesek válaszolnak-e rá. Ideális esetben a válasz tartalmazza a probléma megoldásának tervezett időpontját, és azt, milyen lépéseket fognak tenni a megoldás érdekében.
                            </p>
                            <p class="badge badge--waiting-for-solution">Megoldásra vár</p>
                        </div>
                        <div class="timeline__container__box">
                            <p>Ha a címzett nem válaszol 30 napon belül, akkor a problémát újraküldjük, majd 60 nap után automatikusan megoldatlannak minősítjük. Ez jelenik meg az olyan bejelentéseknél is, melyekre az illetékesek már a megoldás időpontját és módját tartalmazó levéllel válaszoltak, de mégsem oldották meg a problémát.
                            </p>
                            <p class="badge badge--unresolved">Megoldatlan</p>
                        </div>
                    </li>
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_CLOCK, ['class' => 'icon filter__icon'])?>
                            </span>
                            Írj nekünk, ha megoldották a bejelentésed
                        </h2>
                        <div class="timeline__container__box">
                            <p>
                                Automatikusan értesítünk e-mailben, hogy nézd meg a probléma helyszínét és igazold, hogy az illetékes valóban megoldotta-e a problémát.
                            </p>
                            <p class="badge badge--waiting-for-response">Megerősítésre vár</p>
                        </div>
                    </li>
                    <li class="timeline__container">
                        <h2 class="timeline__container__title">
                            <span class="timeline__container__title__icon">
                                <?= SVG::icon(SVG::ICON_CHECKMARK, ['class' => 'icon filter__icon'])?>
                            </span>
                            Megerősítés érkezik
                        </h2>
                        <div class="timeline__container__box">
                            <p>
                                Ha te bejelentőként, vagy mások hozzászólásban megerősítik, hogy a problémát az illetékes elhárította, úgy az ügyet megoldottnak tekintjük.
                            </p>
                            <p class="badge badge--resolved">Megoldódott</p>
                        </div>
                        <div class="timeline__container__box">
                            <p>
                                Negatív vélemény esetén a bejelentést újra elküldjük az illetékesnek felülvizsgálásra.
                            </p>
                            <p class="badge badge--waiting-for-answer">Válaszra vár</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

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
