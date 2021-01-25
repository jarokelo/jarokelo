<?php

use app\assets\AppAsset;

use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\CookieConsentAsset;
use app\components\helpers\CookieConsent;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$bundle = AppAsset::register($this);
\app\assets\PasswordValidatorAsset::register($this);

$this->beginPage() ?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Url::base() ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?= Url::base() ?>/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?= Url::base() ?>/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?= Url::base() ?>/manifest.json">
    <link rel="mask-icon" href="<?= Url::base() ?>/safari-pinned-tab.svg" color="#5bbad5">
    <?php $this->head() ?>
    <script>
        var baseUrl = '<?= Url::base(true) ?>';
        var loaderMessage = '<?= Yii::t('const', 'loader-message') ?>';
    </script>
</head>
<body class="body--light">

<?php
CookieConsentAsset::register($this);

if (CookieConsent::isAllowed() && !empty(Yii::$app->params['gtm']['key'])): ?>
    <!-- Google Tag Manager -->
    <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?=Yii::$app->params['gtm']['key']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(
            {'gtm.start': new Date().getTime(),event:'gtm.js'}
        );var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?=Yii::$app->params['gtm']['key']; ?>');</script>
    <!-- End Google Tag Manager -->
<?php endif; ?>

<?php $this->beginBody() ?>
<header class="header">
    <div class="container print--hidden">
        <div class="header__user-menu">
            <a href="" class="header__user-menu__navigation-icon js--toggle-navigation">
                <?= SVG::icon(SVG::ICON_MENU) ?>
            </a>

            <a href="<?= ((Yii::$app->user->isGuest) ? Link::to([Link::AUTH_LOGIN]) : Link::to([Link::PROFILE_MANAGE])) ?>" class="header__user-menu__menu-icon">
                <?= ((Yii::$app->user->isGuest) ? SVG::icon(SVG::ICON_CIRCLE_USER) : Html::img(\app\models\db\User::getPictureUrl(Yii::$app->user->identity->id))) ?>
            </a>

            <?php
            $items = [];

            if (Yii::$app->user->isGuest) {
                $items[] = [
                    'options' => ['class' => 'header__user-menu__item'],
                    'label' => Yii::t('menu', 'login'),
                    'url' => Link::to(Link::AUTH_LOGIN),
                    'linkOptions' => ['class' => 'header__user-menu__link navigation__link'],
                ];
                $items[] = [
                    'options' => ['class' => 'header__user-menu__item'],
                    'label' => Yii::t('menu', 'register'),
                    'url' => Link::to(Link::AUTH_REGISTER),
                    'linkOptions' => ['class' => 'header__user-menu__link header__user-menu__link--button'],
                ];
            } else {
                $user = Yii::$app->getUser()->getIdentity();
                $items[] = [
                    'label' => $user->first_name,
                    'url' => Link::to(Link::PROFILE),
                    'linkOptions' => [
                        'class' => 'header__user-menu__link header__user-menu__link--button',
                    ],
                ];
            }

            echo Nav::widget([
                'options' => ['class' => 'header__user-menu__list'],
                'items' => $items,
            ]);
            ?>
        </div>

        <h1 class="logo logo--default">
            <span class="visuallyhidden">myProject</span>

            <a href="<?= Link::to([Link::HOME]) ?>">
                Logo
            </a>
        </h1>

        <?= $this->render('_menu', ['bundle' => $bundle]) ?>
    </div>
</header>

<main>
    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'homeLink' => isset($this->params['breadcrumbs_homeLink']) ? $this->params['breadcrumbs_homeLink'] : null,
        'options' => [
            'class' => 'site-breadcrumb',
        ],
    ]) ?>
    <?= \app\components\AlertWidget::showAlerts(); ?>
    <?= $content ?>
</main>
<?= $this->render('_footer') ?>

<div id="cookie_consent_content"></div>
<style>
    .cc-compliance .cc-ALLOW {
        margin-left: .5em;
        font-weight: bold;
        border-radius: 2.7777777778em;
    }

    .cc-compliance .cc-ALLOW:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .cc-compliance .cc-DISMISS {
        font-weight: normal;
    }
</style>
<script>
    function initCookieConsent() {
        new window.CookieConsent(
            {
                container: document.getElementById('cookie_consent_content'),
                type: 'opt-in',
                palette: {
                    popup: {
                        background: "#9bd158",
                        text: "#fff"
                    },
                    button: {
                        background: 'transparent',
                        border: '#fff',
                        text: "#fff"
                    },
                    highlight: {
                        background: 'transparent',
                        border: 'transparent',
                        text: '#fff'
                    },
                },
                law: {
                    regionalLaw: false,
                },
                location: false,
                content: {
                    message: '<?= Yii::t('app', 'footer.cookie.message') ?>',
                    dismiss: '<?= Yii::t('app', 'footer.cookie.deny') ?>',
                    allow: '<?= Yii::t('app', 'footer.cookie.allow') ?>',
                    deny: '<?= Yii::t('app', 'footer.cookie.deny') ?>',
                    link: '<?= Yii::t('app', 'footer.cookie.learn_more') ?>',
                    policy: '<?= Yii::t('app', 'footer.cookie.policy') ?>',
                    href: '<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TOS]) ?>'
                },
                cookie: {
                    sameSite: 'None',
                    secure: true,
                    domain: ';'
                }
            }
        );
    }
</script>

<?php $this->registerJs('initCookieConsent()') ?>
<?php $this->endBody() ?>
<?php $this->registerJs('Dropzone.autoDiscover = false;', \yii\web\View::POS_END) ?>

<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?= Yii::$app->authClientCollection->clients['facebook']->clientId ?>',
            xfbml      : true,
            version    : 'v2.7'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/hu_HU/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

</body>
</html>
<?php $this->endPage() ?>
