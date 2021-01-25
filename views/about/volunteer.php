<?php
use app\components\helpers\SVG;
use yii\helpers\Html;
?>

<aside class="hero hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="hidden--mobile">
            <div class="hero__background hero--volunteer"></div>
        </div>
        <div class="hidden--desktop">
            <div class="hero__background hero--volunteer"></div>
        </div>
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-10 col-sm-8">
                    <h2 class="heading heading--1 hero__title">Jelentkezz önkéntesnek!</h2>
                    <p class="hero__lead">
                        Csatlakozz egy lelkes csapathoz és vedd ki a részed abból, hogy minél több problémát segítsünk megoldani településeden.
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="container container--box--desktop">
    <div class="container__row--tablet">
        <div class="flex">
            <div class="col-xs-12 col--off--tablet">
                <div class="hidden--mobile">
                    <h2 class="heading heading--2">Jelenleg őket keressük</h2>
                </div>
                <div class="hidden--desktop">
                    <div class="container form--padding section--grey">
                        <h2 class="heading heading--3 offset--off">Jelenleg őket keressük</h2>
                    </div>
                </div>
            </div>
        </div>
        <ul class="flex">
            <li class="col-xs-12 col-lg-4 col--off--tablet">
                <div class="accordion accordion--mobile">
                    <div class="accordion__title">
                        <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'accordion__title__icon filter__icon']) ?>
                        <p class="accordion__title__text heading--3">Backend fejlesztő</p>
                        <?= SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'accordion__title__dd filter__icon']) ?>
                        <?= SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'accordion__title__dd accordion__title__dd--active filter__icon']) ?>
                    </div>

                    <div class="accordion__content">
                        <div class="accordion__media">
                            <img src="<?= \app\assets\AppAsset::register($this)->baseUrl; ?>/images/job_backend.png" alt="">
                        </div>
                        <p class="heading--3 hidden--mobile">Backend fejlesztő</p>
                        <p>
                            A weboldal fejlesztéséhez keresünk PHP programozót, aki jártas még a SQL-ben és a kliens oldali technológiákban.
                        </p>
                        <div class="br hidden--mobile"></div>
                        <p>
                            Ha segítenél az oldal fejlesztésében, akkor küldd el jelentkezésed az alábbi címre: <a href="mailto:onkentes@myproject.hu?subject=Backend%20fejlesztő" class="accordion__content__link link link--info">onkentes@myproject.hu</a><span class="hidden--mobile--inline">.</span>
                        </p>
                    </div>
                </div>
            </li>
            <li class="col-xs-12 col-lg-4 col--off--tablet">
                <div class="accordion accordion--mobile">
                    <div class="accordion__title">
                        <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'accordion__title__icon filter__icon']) ?>
                        <p class="accordion__title__text heading--3">Ügykezelő</p>
                        <?= SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'accordion__title__dd filter__icon']) ?>
                        <?= SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'accordion__title__dd accordion__title__dd--active filter__icon']) ?>
                    </div>

                    <div class="accordion__content">
                        <div class="accordion__media">
                            <img src="<?= \app\assets\AppAsset::register($this)->baseUrl; ?>/images/job_ugykezelo.png" alt="">
                        </div>
                        <p class="heading--3 hidden--mobile">Ügykezelő</p>
                        <p>
                            A beérkező bejelentések ellenőrzésében, továbbításában van szükségünk segítségre. Ha egy bizonyos környéken, kerületben ismerős vagy, azt is írd meg nekünk.
                        </p>
                        <p>
                            Ha segítenél problémákat megoldani, akkor küldd el jelentkezésed az alábbi címre: <a href="mailto:onkentes@myproject.hu?subject=Ügykezelő" class="accordion__content__link link link--info">onkentes@myproject.hu</a><span class="hidden--mobile--inline">.</span>
                        </p>
                    </div>
                </div>
            </li>
            <li class="col-xs-12 col-lg-4 col--off--tablet">
                <div class="accordion accordion--mobile">
                    <div class="accordion__title">
                        <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'accordion__title__icon filter__icon']) ?>
                        <p class="accordion__title__text heading--3">Közösségi szöcske</p>
                        <?= SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'accordion__title__dd filter__icon']) ?>
                        <?= SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'accordion__title__dd accordion__title__dd--active filter__icon']) ?>
                    </div>

                    <div class="accordion__content">
                        <div class="accordion__media">
                            <img src="<?= \app\assets\AppAsset::register($this)->baseUrl; ?>/images/job_blogger.png" alt="">
                        </div>
                        <p class="heading--3 hidden--mobile">Közösségi szöcske</p>
                        <p>
                            Ha szeretsz blogolni és mindenről megvan a véleményed, te vagy a mi emberünk. A myProject.hu-n minden nap megjelenik olyan sztori, amiről lehet írni.
                        </p>
                        <p>
                            Ha ápolnád közösségi csatornáinkat, akkor küldd el jelentkezésed az alábbi címre: <a href="mailto:onkentes@myproject.hu?subject=Közösségi%20szöcske" class="accordion__content__link link link--info">onkentes@myproject.hu</a><span class="hidden--mobile--inline">.</span>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<?= $this->render('/_snippets/_hero-bottom'); ?>
