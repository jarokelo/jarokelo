<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $bundle \app\assets\AppAsset
 * @var View $this
 */

$blogUrl = Url::to('http://myproject.blog.hu/?utm_source=myproject&utm_medium=main-menu', true);
?>

<nav class="navigation">
    <h2 class="visuallyhidden"><?=Yii::t('label', 'generic.navigation'); ?></h2>

    <a href="" class="navigation__close js--toggle-navigation">
        <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'navigation__close__icon']) ?>
    </a>

        <?php
        $user = Yii::$app->user;

        if ($user->isGuest): ?>
            <div class="user">
                <p class="user__text"><?= Yii::t('menu', 'login_to_view_profile'); ?></p>
                <a href="<?= Link::to(Link::AUTH_LOGIN); ?>" class="button button--large button--primary"><?= Yii::t('menu', 'login') ?></a>
            </div>
        <?php else: ?>
            <a href="<?= Link::to(Link::PROFILE); ?>" class="user">
                <div class="row">
                    <div class="user__avatar">
                        <img src="<?= \app\models\db\User::getPictureUrl($user->id) ?>" alt="" class="" />
                    </div>
                    <div class="col-xs middle-xs">
                        <div class="user__data">
                            <strong class="user__name"><?= $user->getIdentity()->getFullName() ?></strong>
                            <address class="user__location"><?= ($user->getIdentity()->city ? $user->getIdentity()->city->name : Yii::t('profile', 'top.setCity')) . ($user->getIdentity()->district ? ', ' . $user->getIdentity()->district->name : '') ?></address>
                        </div>
                    </div>
                </div>
            </a>
        <?php endif; ?>

    <?php

    echo \app\components\Menu::widget([
        'items' => [
            ['label' => Yii::t('menu', 'report'), 'url' => Link::to(Link::REPORTS), 'options' => ['class' => 'navigation__item']],
            ['label' => Yii::t('menu', 'new_report'), 'url' => Link::to(Link::CREATE_REPORT), 'options' => ['class' => 'navigation__item']],
            ['label' => '<span>' . Yii::t('menu', 'support') . '</span>', 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_SUPPORT]), 'options' => ['class' => 'navigation__item']],
            [
                'label' => Yii::t('menu', 'participate')
                    . '<span class="hidden--desktop icon-container icon-down">' . SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'icon icon--large icon--before back-to-top__icon']) . '</span>'
                    . '<span class="hidden--desktop icon-container icon-up">' . SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'icon icon--large icon--before back-to-top__icon']) . '</span>',
                'url' => '#',
                'submenuTemplate' => '<div class="dropdown-content dropdown-participate">
                    <div class="dropdown__body">
                        <strong class="dropdown__title">' . Yii::t('menu', 'dropdown.title2') . '</strong>
                        <p class="dropdown__lead">' . Yii::t('menu', 'dropdown.lead2') . '</p>
                    </div>
                    <ul class="dropdown-menu">
                        {items}
                    </ul>
                </div>',
                'items' => [
                    ['label' => Yii::t('menu', 'volunteer'), 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_VOLUNTEER]), 'options' => ['class' => 'navigation__item navigation__item-last']],
                ],
                'options' => ['class' => 'navigation__item dropdown navigation-dropdown'],
            ],
            [
                'label' => Yii::t('menu', 'apps.blog'),
                'url' => $blogUrl,
                'options' => ['class' => 'navigation__item hide--last-1'],
                'template' => '<a class="navigation__link" href="{url}" target="_blank">{label}</a>',
            ],
            [
                'label' => Yii::t('menu', 'about'),
                'url' => Link::to(Link::ABOUT),
                'items' => [
                    [
                        'label' => Yii::t('menu', 'apps.blog'),
                        'url' => $blogUrl,
                        'options' => ['class' => 'navigation__item show--last'],
                        'template' => '<a class="navigation__link" href="{url}" target="_blank">{label}</a>',
                    ],
                    ['label' => Yii::t('menu', 'contact'), 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_CONTACT]), 'options' => ['class' => 'navigation__item']],
                    ['label' => Yii::t('menu', 'statistics'), 'url' => Link::to([Link::STATISTICS]), 'options' => ['class' => 'navigation__item']],
                    ['label' => Yii::t('menu', 'team'), 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TEAM]), 'options' => ['class' => 'navigation__item']],
                    ['label' => Yii::t('menu', 'partners'), 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_PARTNERS]), 'options' => ['class' => 'navigation__item']],
                    ['label' => Yii::t('menu', 'tos'), 'url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TOS]), 'options' => ['class' => 'navigation__item']],
                ],
                'options' => ['class' => 'navigation__item dropdown'],
            ],
        ],
    ]);
    ?>

</nav>

<script>
    function initDropdown() {
        $('.navigation-dropdown').click(function() {
            $('.dropdown-participate').toggleClass('active');
            $('.icon-up').toggleClass('active');
            $('.icon-down').toggleClass('inactive');
        });
    }
</script>

<?php
$this->registerJs('initDropdown()');
