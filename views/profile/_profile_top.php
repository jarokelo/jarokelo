<?php

use app\components\helpers\Link;
use yii\helpers\Html;
use yii\helpers\Url;
use \app\models\db\Report;

/** @var \app\models\db\User $user */
/** @var bool $view */

$view = isset($view) && $view == true;

?><div class="profile profile__top">
    <div class="container profile__container">
        <div class="row">
            <div class="col-xs-12 col-lg-3 profile__user">
                <div class="profile__user clearfix">
                    <div class="profile__avatar">
                        <img src="<?= Url::to(\app\models\db\User::getPictureUrl($user)) ?>" alt="" style="" />
                    </div>
                    <div class="profile__userinfo">
                        <h2><?= $user->getFullName(); ?></h2>
                        <?php if (!$view || ($view && $user->city)): ?>
                            <p><?= ($user->city ? $user->city->name : Html::a(Yii::t('profile', 'top.setCity'), Link::to([Link::PROFILE_MANAGE]))) . ($user->district ? ', ' . $user->district->name : ''); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-lg-7">
                <div class="profile__topcontainer">
                    <div class="profile__topitem">
                        <span class="uppercase"><?= Yii::t('label', 'generic.toplist'); ?></span>
                        <div class="number"><?= $user->getRank(); ?>.</div>
                        <?= Yii::t('label', 'generic.toplist.score', ['score' => $user->getPoints(false)]); ?>
                        <div class="topitem__border"></div>
                    </div>
                    <div class="profile__topitem hidden--desktop">
                        <span class="uppercase"><?php echo Yii::t('report', 'filter.label-after-type-select') ?></span>
                        <div class="number"><?= Report::countUserReports($user->id); ?></div>
                        <?= Report::countUserResolved($user->id); ?> <?php echo Yii::t('user', 'report.resolved'); ?>
                    </div>
                    <div class="profile__topitem hidden--mobile--table-cell">
                        <div class="number"><?= Report::countUserReports($user->id); ?></div>
                        <?= Yii::t('profile', 'report.count'); ?>
                    </div>
                    <div class="profile__topitem hidden--mobile--table-cell">
                        <div class="number"><?= Report::countUserInProgress($user->id); ?></div>
                        <?= Yii::t('profile', 'report.inprogress'); ?>
                    </div>
                    <div class="profile__topitem hidden--mobile--table-cell">
                        <div class="number"><?= Report::countUserResolved($user->id); ?></div>
                        <?= Yii::t('profile', 'report.solved'); ?>
                    </div>
                    <div class="profile__topitem hidden--mobile--table-cell">
                        <div class="number"><?= Report::countUserUnresolved($user->id); ?></div>
                        <?= Yii::t('profile', 'report.unsolved'); ?>
                        <div class="topitem__border"></div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-lg-2">
                <?php
                if (!$view):
                    echo \yii\bootstrap\Nav::widget([
                        'items' => [
                            ['label' => Yii::t('profile', 'menu.my_reports', ['count' => Report::countUserReports($user->id)]), 'url' => Link::to(Link::PROFILE), 'active' => (Yii::$app->request->absoluteUrl === Link::to(Link::PROFILE))],
                            ['label' => Yii::t('profile', 'menu.drafts', ['count' => Report::countUserDrafts($user->id)]), 'url' => Link::to(Link::PROFILE_DRAFTS), 'active' => (Yii::$app->request->absoluteUrl === Link::to(Link::PROFILE_DRAFTS))],
                            ['label' => Yii::t('profile', 'menu.manage'), 'url' => Link::to(Link::PROFILE_MANAGE), 'active' => (Yii::$app->request->absoluteUrl === Link::to(Link::PROFILE_MANAGE))],
                            ['label' => Yii::t('profile', 'menu.logout'), 'url' => Link::to(Link::AUTH_LOGOUT), 'linkOptions' => ['data-method' => 'post']],
                        ],
                        'options' => [
                            'class' => 'profile__toplinks',

                        ],
                        'activateItems' => true,
                        'activateParents' => true,
                    ]);
                endif;
                ?>
            </div>
        </div>
    </div>
</div>
