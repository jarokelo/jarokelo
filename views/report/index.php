<?php
use app\components\helpers\SVG;
use app\components\helpers\Link;
use app\components\widgets\Pjax;
use yii\helpers\Html;
use \app\assets\AppAsset;

$bundle = AppAsset::register($this);

/* @var \app\models\db\Report[] $myLastReports */
/* @var \app\models\db\search\ReportSearch $searchModel */
/* @var \app\models\db\Report $highlighted[] */
/* @var \app\models\db\Report $latest[] */

$assetUrl = AppAsset::register($this)->baseUrl;
?>

<section class="section--grey">
    <?php if (Yii::$app->user->isGuest): ?>
    <?= $this->render('@app/views/_snippets/_hero-main-top.php');?>
    <?php else: ?>
    <article class="hero hero--mylastreports">
        <div class="section container container--small">
            <ul class="hero__links list list--horizontal hidden--mobile">
                <li>
                    <a <?= Link::to(Link::PROFILE); ?>" class="link link--default">
                        <?= Yii::t('label', 'generic.all_my_reports'); ?>
                        <?= SVG::icon(SVG::ICON_DOCUMENTS, ['class' => 'link__icon icon icon--before'])?>
                    </a>
                </li>
            </ul>
            <h2 class="hero__title--mylastreports"><?=Yii::t('report', 'last_reports');?></h2>

            <ul class="row list list--cards">
            <?php foreach ($myLastReports as $report): ?>
                <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
                <?= $this->render('_card', [
                    'report' => $report,
                    'wideOnMobile' => true,
                ]); ?>
                </li>
            <?php endforeach; ?>

            <li class="col-xs-12 col-md-6 col-lg-<?= 12 - (count($myLastReports) * 3) ?>">
                <article class="card card--newproblem">
                    <h3 class="card__title"><?=Yii::t('report', 'report.new-report.title');?></h3>
                    <p><?=Yii::t('report', 'report.new-report.about');?></p>
                    <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
                </article>
            </li>
            </ul>
        </div>
    </article>
    <?php endif; ?>

<?php
Pjax::begin([
    'id' => 'index-reports',
    'enablePushState' => true,
    'enableReplaceState' => true,
    'formSelector' => '#front-search-form',
    'linkSelector' => false,
]);
?>

    <div class="filter container">
        <?= $this->render('_front-filter', [
            'type' => null,
            'model' => $searchModel,
        ]);
        ?>
    </div>

    <?= $this->render('_front-search', [
        'model' => $searchModel,
    ]); ?>

    <?php /* Highlighted reports */ ?>
    <?php if (count($highlighted) > 0): ?>
    <section class="section container">
        <div class="section__header">
            <a href="<?= Link::to([Link::REPORTS_HIGHLIGHTED, 'city_id' => $citySlug]) ?>" class="link link--default section__link hidden--mobile">
                <?= Yii::t('menu', 'featured_more') . SVG::icon(SVG::ICON_CHEVRON_RIGHT, ['class' => 'link__icon icon icon--after']) ?>
            </a>
            <h2 class="heading heading--4"><?= Yii::t('menu', 'featured'); ?></h2>
        </div>

        <ul class="list list--cards row">
        <?php foreach ($highlighted as $report): ?>
            <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
            <?= $this->render('_card', [
                'report' => $report,
            ]); ?>
            </li>
        <?php endforeach; ?>
        </ul>

        <div class="section__footer hidden--desktop">
            <a href="<?= Link::to([Link::REPORTS_HIGHLIGHTED, 'city_id' => $citySlug]); ?>" class="link link--default section__link">
                <?= Yii::t('menu', 'featured_more') . SVG::icon(SVG::ICON_CHEVRON_RIGHT, ['class' => 'link__icon icon icon--after']) ?>
            </a>
        </div>
    </section>
    <?php endif; ?>

    <?php if (count($latest) > 0): ?>
    <section class="section container">
        <div class="section__header">
            <a href="<?= Link::to([Link::REPORTS_FRESH, 'city_id' => $citySlug]) ?>" class="link link--default section__link hidden--mobile">
                <?= Yii::t('menu', 'new_more') . SVG::icon(SVG::ICON_CHEVRON_RIGHT, ['class' => 'link__icon icon icon--after']) ?>
            </a>
            <h2 class="heading heading--4"><?= Yii::t('menu', 'new'); ?></h2>
        </div>

        <ul class="list list--cards row">
        <?php foreach ($latest as $report): ?>
            <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
            <?= $this->render('_card', [
                'report' => $report,
            ]); ?>
            </li>
        <?php endforeach; ?>
        </ul>

        <div class="section__footer hidden--desktop">
            <a href="<?= Link::to([Link::REPORTS_FRESH, 'city_id' => $citySlug]) ?>" class="link link--default section__link">
                <?= Yii::t('menu', 'new_more') . SVG::icon(SVG::ICON_CHEVRON_RIGHT, ['class' => 'link__icon icon icon--after']) ?>
            </a>
        </div>
    </section>
    <?php endif; ?>

    <?php if (count($highlighted) === 0 && count($latest) === 0): ?>
        <?= $this->render('@app/views/_snippets/_no-reports-found.php', ['link' => Link::to(Link::HOME)]) ?>
    <?php endif; ?>

<?php Pjax::end() ?>

    <?= $this->render('/_snippets/_support_box'); ?>

    <?= $this->render('/_snippets/_mobile_box_green'); ?>
</section>
