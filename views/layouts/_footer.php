<?php
use app\assets\AppAsset;
use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\Report;
use app\models\db\User;
use yii\helpers\Url;

$bundle = AppAsset::register($this);
?>

<style>
    .center .col-xs-12 {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mt20 {
        margin-top:20px;
    }
</style>

<a href="#" class="back-to-top">
    <?= SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'icon icon--large icon--before back-to-top__icon']) ?>
    &nbsp;
    <?= Yii::t('label', 'footer.back_to_top'); ?>
</a>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="footer__box footer__box--horizontal col-xs-12 col-md-3 hidden--mobile">
                <h2 class="footer__box__title"><?= Yii::t('label', 'footer.write_to_us.0'); ?></h2>
                <p class="footer__box__lead">
                    <?= Yii::t('label', 'footer.write_to_us.1'); ?><br>
                    <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_CONTACT]); ?>"><strong><?= Yii::t('label', 'footer.write_to_us'); ?>!</strong></a>
                </p>
                <p class="footer__box__lead">
                    <?= Yii::t('label', 'footer.join_us.0'); ?><br>
                    <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_VOLUNTEER]); ?>"><strong><?= Yii::t('label', 'footer.join_us.1'); ?></strong></a>
                </p>
                <p class="footer__box__lead">
                    <?= Yii::t('label', 'footer.institution.0'); ?><br>
                    <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_BUREAU]); ?>"><strong><?= Yii::t('label', 'footer.institution.1'); ?></strong></a>
                </p>
            </div>

            <div class="col-xs-12 hidden--desktop">
                <div class="row center-xs">
                    <ul class="col-xs-12 footer__list list list--inline">
                        <li class="footer__list__item">
                            <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]); ?>"><?= Yii::t(
                                'label',
                                'footer.how_it_works'
                            ); ?></a>
                        </li>
                        <li class="footer__list__item">
                            <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_SUPPORT]); ?>"><?= Yii::t(
                                'label',
                                'footer.support'
                            ); ?></a>
                        </li>
                        <li class="footer__list__item">
                            <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_CONTACT]); ?>"><?= Yii::t(
                                'label',
                                'footer.write_to_us'
                            ); ?></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer__box footer__box--horizontal col-xs-12 col-sm-3 hidden--mobile">
                <h2 class="footer__box__title"><?= Yii::t('label', 'footer.follow_us'); ?></h2>
                <?= $this->render('@app/views/layouts/_social-links') ?>
            </div>

            <div class="footer__box footer__box--horizontal col-xs-12 hidden--desktop">
                <h2 class="footer__box__title"><?= Yii::t('label', 'footer.follow_us'); ?></h2>
                <?= $this->render('@app/views/layouts/_social-links') ?>
            </div>

            <div class="footer__box footer__box--horizontal col-xs-12 col-md-3 hidden--mobile">
                <h2 class="footer__box__title"><?= Yii::t('label', 'footer.know_more'); ?></h2>
                <ul class="list list--default">
                    <li>
                        <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]); ?>"><?= Yii::t(
                            'label',
                            'footer.know_more.how'
                        ); ?></a>
                    </li>
                    <li>
                        <a class="link" href="<?= Link::to([Link::STATISTICS]); ?>"><?= Yii::t(
                            'label',
                            'footer.know_more.statistics'
                        ); ?></a>
                    </li>
                    <li>
                        <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TEAM]); ?>"><?= Yii::t(
                            'label',
                            'footer.know_more.team'
                        ); ?></a>
                    </li>
                    <li>
                        <a class="link" href="<?= Url::to(['/widget/configure']); ?>"><?= Yii::t(
                            'label',
                            'footer.know_more.widget'
                        ); ?></a>
                    </li>
                    <li>
                        <a class="link" href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_ANNUALREPORTS]); ?>"><?= Yii::t(
                            'label',
                            'footer.know_more.annual_reports'
                        ); ?></a>
                    </li>
                </ul>
            </div>
            <?php if ('support' != Yii::$app->controller->action->id): // Hiding this section from support page ?>
                <div class="footer__box footer__box--horizontal col-xs-12 col-md-3 hidden--mobile">
                    <h2 class="footer__box__title"><?= Yii::t('label', 'footer.stat.title'); ?></h2>
                    <dl class="statistics statistics--grid">
                        <dt class="statistics__value">
                            <?= Report::countResolved() ?>
                        </dt>
                        <dd class="statistics__label">
                            <?= Yii::t('label', 'footer.stat.resolved') ?>
                        </dd>
                    </dl>
                    <dl class="statistics statistics--grid">
                        <dt class="statistics__value">
                            <?= Report::countUnresolved() ?>
                        </dt>
                        <dd class="statistics__label">
                            <?= Yii::t('label', 'footer.stat.unresolved') ?>
                        </dd>
                    </dl>
                    <dl class="statistics statistics--grid">
                        <dt class="statistics__value">
                            <?= User::countActive() ?>
                        </dt>
                        <dd class="statistics__label">
                            <?= Yii::t('label', 'footer.stat.users') ?>
                        </dd>
                    </dl>
                    <dl class="statistics statistics--grid">
                        <dt class="statistics__value">&nbsp;</dt>
                        <dd class="statistics__label">&nbsp;</dd>
                    </dl>
                </div>
            </div>
        <?php endif; ?>
        <div class="footer__box footer__box--vertical hidden--mobile">
            <h2 class="footer__box__title"><?= Yii::t('label', 'footer.our_partners'); ?></h2>
            <ul class="list list--sponsors">
                <li>
                    <a href="#" target="_blank"><img src="" alt="Szponzor"></a>
                </li>
            </ul>
        </div>


        <div class="row center">
            <div class="col-xs-12">
                <a href="/profile/switch-language?language=en">English</a>
                &nbsp;<a href="/profile/switch-language?language=hu">Magyar</a>
            </div>

            <div class="col-xs-12 mt20">
                <small class="footer__copyright">&copy;
                    <time datetime="<?=date('Y')?>"><?=date('Y')?></time>
                    <?= Yii::t('meta', 'og.site_name') ?>
                </small>
            </div>
        </div>
    </div>
</footer>
