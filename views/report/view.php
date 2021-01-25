<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\Report;
use yii\helpers\Url;
use app\components\helpers\Html;
use app\assets\AppAsset;

$bundle = AppAsset::register($this);

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */
/* @var string $source */

$urlToShare = Url::base(true) . Url::to();
$this->registerJs("
    site.urlToShare = '" . $urlToShare . "';
");

?>


<section class="section--grey">
    <article class="report custom-col">
        <div class="container row">
            <div id="report-description" class="col-xs-12 col-md-12 col-lg-8">
                <?php
                if (isset($source) && $source == Report::SOURCE_EDM) {
                    echo $this->render('_institution_info', [
                        'reportUniqueName' => $model->uniqueName,
                        'infoLink' => Url::to(['about/bureau']),
                        'buttonLink' => $model->getUrl(Report::SOURCE_PDF),
                    ]);
                } ?>
                <header class="report__header">
                    <span class="badge badge--<?= Yii::t('const', 'report.class.' . $model->status); ?>">
                        <?= Yii::t('const', 'report.status.' . $model->status) ?>
                    </span>
                    <h1 class="report__title heading heading--2"><?= $model->name ?></h1>
                </header>

                <ul class="report__tabs__header">
                    <li class="report__tabs__header__item">
                        <a class="report__tabs__header__link report__tabs__header__link--active" href="#details"><?= Yii::t('report', 'mobile-view.tab1.title') ?></a>
                    </li>
                    <li class="report__tabs__header__item">
                        <a class="report__tabs__header__link" href="#comments">
                            <?= Yii::t('report', 'mobile-view.tab2.title') ?>
                            <span class="report__tabs__header__count">(<?= $model->getCommentCount(); ?>)</span>
                        </a>
                    </li>
                </ul>

                <div tab="details" class="report__tabs__content report__tabs__content--details report__tabs__content--active">
                    <div class="report__content">

                        <div class="report__details">
                            <div class="report__reporter">
                                <div class="report__reporterimg">
                                    <img src="<?= \app\models\db\User::getPictureUrl($model->anonymous ? null : $model->user_id) ?>" alt="" class="report__reporter__media" />
                                </div>
                                <div
                                    class="report__author"><?= (($model->anonymous || $model->user_id === null) ? Yii::t('report', 'report.anonymous') : Html::a($model->user->fullName, Link::to([Link::PROFILES, $model->user_id]), ['class' => 'link link--black'])) ?></div>
                                <time class="report__date"><?= Yii::$app->formatter->asDate($model->created_at); ?></time>
                            </div>

                            <div class="report__category">
                                <?= SVG::icon(SVG::ICON_CATEGORY, ['class' => 'report__category__icon icon']) ?>
                                <?= Html::a($model->reportCategory->name, Link::to(Link::REPORTS, ['category' => $model->report_category_id]), ['class' => 'link link--black']) ?>
                            </div>
                            <?php if ($model->institution !== null) { ?>
                                <div class="report__institution">
                                    <?= SVG::icon(SVG::ICON_INSTITUTION, ['class' => 'report__institution__icon icon']) ?>
                                    <?= Html::a($model->institution->name, Link::to(Link::REPORTS, ['institution' => $model->institution_id]), ['class' => 'link link--black']) ?>
                                </div>
                            <?php } ?>
                        </div>

                        <?php
                        $partner = \app\components\helpers\Report::getPartner($model);
                        ?>

                        <?php if ($partner !== false) { ?>
                            <div class="report__partner report__partner--<?= $partner['id'] ?>">
                                <a href="<?= $partner['url'] ?>" target="_blank">
                                    <p><?php if (isset($partner['coop_partner'])): ?>
                                        <?= $partner['coop_partner']; ?>
                                    <?php else: ?>
                                        <?= Yii::t('report', 'coop_partner') ?></p>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col--on report__partner__image">
                                            <?= Html::img(
                                                $partner['logo']
                                            ); ?>
                                        </div>
                                        <div class="col-xs"><?= $partner['title'] ?><br/>
                                            <span class="report__partner__about"><?= $partner['text'] ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>

                        <?php
                        $mediaItems = $model->getPicturesAndVideos();
                        if (count($mediaItems) > 0) {
                        ?>
                            <div class="report__media-container">
                                <div class="report__media owl-carousel" data-slider-id="report">
                                    <?php
                                    foreach ($mediaItems as $item) {
                                        ?>
                                        <a href="<?= (isset($item['urlFrame']) ? $item['urlFrame'] : $item['url']) ?>" data-exthumbimage="<?= $item['image'] ?>" class="report__media__item owl-item unwrap" title="<?= $model->name; ?>">
                                            <div class="report__media__item__wrapper">
                                                <div class="report__media__item__img" style="background-image: url(<?= $item['image']; ?>);"></div>
                                            </div>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="owl-thumbs report__thumbs flex center-xs" data-slider-id="report">
                                    <?php
                                    foreach ($mediaItems as $item) {
                                        ?>
                                        <a href="<?= (isset($item['urlFrame']) ? $item['urlFrame'] : $item['url']) ?>" data-exthumbimage="<?= $item['image'] ?>" class="owl-thumb-item report__thumbs__item">
                                            <div class="report__thumbs__item__img" style="background-image: url(<?= $item['image']; ?>);"></div>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="owl-nav report__media__nav"></div>
                            </div>
                        <?php
                        }
                        ?>

                        <p class="report__description"><?= Html::formatText($model->description, 'link--default link--info') ?></p>

                        <?php
                        echo $this->render('/_snippets/_mapbox', [
                            'options' => [
                                'title' => Yii::t('report', 'problem_place'),
                                'selectors' => [
                                    'map' => '#map',
                                ],
                                'zoom' => $model->zoom,
                                'center' => [
                                    'lat' => Report::formatCoordinate($model->latitude),
                                    'lng' => Report::formatCoordinate($model->longitude),
                                ],
                            ],
                        ]);

                        $locationBreak = explode(',', $model->user_location);
                        $location_city = isset($locationBreak[0]) ? $locationBreak[0] : null;
                        $location_address = isset($locationBreak[1]) ? $locationBreak[1] : null;
                        ?>

                        <div class="report__map">
                            <div class="report__location">
                                <?= SVG::icon(SVG::ICON_POI, ['class' => 'report__location__icon icon']) ?>
                                <div class="report__location__title"><?= Yii::t('report', 'problem_place'); ?></div>
                                <address class="report__location__address">
                                    <?= (isset($location_city) ? $location_city : ''); ?>
                                    <span
                                        class="report__location__address__district"><?= ($model->district ? $model->district->name . ', ' : '') . (isset($location_address) ? $location_address : '') ?></span>
                                </address>
                            </div>
                            <div id="map" class="report__map__media"></div>
                        </div>

                        <section class="report__social clearfix">
                            <div class="row">
                                <div class="report__follow__container col-xs-12 col-lg-4">
                                    <div class="report__buttons">
                                        <h3 class="report__section-title"><?= Yii::t('report', 'share_it'); ?></h3>
                                        <br>
                                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($model->name) ?>&via=myprojecthu&url=<?= urlencode($urlToShare) ?>" class="button button--social button--twitter">
                                            <?= SVG::icon(SVG::ICON_TWITTER) ?>
                                        </a>
                                        <a href="" class="button button--social button--facebook button--facebook--share">
                                            <?= SVG::icon(SVG::ICON_FACEBOOK_ALT) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="report__follow__container col-xs-12 col-lg-8">
                                    <?= $this->render('_follow_box', [
                                        'model' => $model,
                                    ]); ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-4">
                <section id="comments-sidebar" tab="comments" class="report__tabs__content report__tabs__content--comments">
                    <h3 class="visuallyhidden"><?= Yii::t('report', 'comments'); ?></h3>
                    <?php if ($model->status != Report::STATUS_DELETED): ?>
                        <?= $this->render('_comment-form', [
                            'model' => $model,
                        ]) ?>
                    <?php endif ?>

                    <?= $this->render(
                        '_activity_list',
                        [
                            'model' => $model,
                            'showDonationBox' => $model->isDonationBoxAvailable(),
                        ]
                    ) ?>
                </section>
            </div>
        </div>

    </article>

    <?= $this->render('_similar-reports', [
        'model' => $model,
    ])?>

    <?= $this->render('/_snippets/_hero-bottom-dual'); ?>
</section>
