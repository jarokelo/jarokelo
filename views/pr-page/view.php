<?php
use app\components\helpers\Link;
use yii\helpers\Html;
use app\models\db\Report;
use app\models\db\PrPage;

/* @var \app\models\db\PrPage $model */

$this->registerJsFile(
    'https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['google']['api_key_http'] . '&libraries=places&callback=site.ReportsOnMap.initMap',
    [
        'defer' => true,
        'depends' => [
            yii\web\JqueryAsset::className(),
            app\assets\AppAsset::className(),
        ],
    ]
);

\app\assets\TwitterAsset::register($this);
\app\assets\InstagramAsset::register($this);

?>

<div class="sticky-header">
    <div class="container">
        <div class="row" >
            <div class="col-md-6 col-xs-4 align-middle">
                <h1 class="logo ">
                    <a href="<?= Link::to([Link::PR_PAGE, $model->slug]) ?>">
                        <img class="sticky-header__logo" src="<?= PrPage::getLogoUrl($model) ?>">
                    </a>
                </h1>
            </div>
            <div class="col-md-6 col-xs-8">
                <div class="sticky-header__button text-right">
                    <a class="sticky-header__button-link" style="--color: <?= $model->custom_color ?>;" href="<?= Link::to(Link::CREATE_REPORT, ['prPageId' => $model->id]) ?>">
                        <?= Yii::t('pr_page', 'button.send_report') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-header__line custom-background-color" style="--color: <?= $model->custom_color ?>;"></div>
</div>

<div class="cover-image__mobile">
    <div class="sticky-header__after"></div>
    <?php if (PrPage::getCoverUrl($model)) { ?>
        <img class="cover-image" src="<?= PrPage::getCoverUrl($model) ?>" >
    <?php } ?>
</div>

<div class="container">
    <div class="hidden--mobile">
        <div class="sticky-header__after"></div>
        <?php if (PrPage::getCoverUrl($model)) { ?>
            <img class="cover-image" src="<?= PrPage::getCoverUrl($model) ?>" >
        <?php } ?>
    </div>
    <div class="text-left">
        <h1 class="heading custom-color" style="--color: <?= $model->custom_color ?>;"><?= $model->title ?></h1>
        <div class="row">
            <div class="<?php if ($model->info_web_page || $model->info_email || $model->info_phone || $model->info_address) { ?>
                col-md-9
            <?php } else { ?>
                col-md
            <?php } ?>">
                <?= $model->introduction ?>
            </div>
            <?php if ($model->info_web_page || $model->info_email || $model->info_phone || $model->info_address) { ?>
                <div class="col-md-3 info-box__desktop">
                    <div class="info-box">
                        <?php if ($model->info_web_page) { ?>
                            <div class="col-md-12">
                                <?= Yii::t('pr_page', 'info.web_page') ?>
                                <a class="link link--info" href="https://<?= $model->info_web_page ?>" target="_blank"><?= $model->info_web_page ?></a>
                            </div>
                        <?php } ?>
                        <?php if ($model->info_email) { ?>
                            <div class="col-md-12">
                                <?= Yii::t('pr_page', 'info.email') ?>
                                <a class="link link--info" href="mailto:<?= $model->info_email ?>"><?= $model->info_email ?></a>
                            </div>
                        <?php } ?>
                        <?php if ($model->info_phone) { ?>
                            <div class="col-md-12">
                                <?= Yii::t('pr_page', 'info.phone') ?>
                                <a class="link link--info" href="tel:<?= $model->info_phone ?>"><?= $model->info_phone ?></a>
                            </div>
                        <?php } ?>
                        <?php if ($model->info_address) { ?>
                            <div class="col-md-12">
                                <?= Yii::t('pr_page', 'info.address') ?>
                                <a class="link link--info" href="https://maps.google.com/?q=<?= $model->info_address ?>" target="_blank"><?= $model->info_address ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="hidden--desktop">
    <?php if ($model->info_web_page || $model->info_email || $model->info_phone || $model->info_address) { ?>
        <div class="info-box">
            <?php if ($model->info_web_page) { ?>
                <div class="col-md-12">
                    <?= Yii::t('pr_page', 'info.web_page') ?>
                    <a class="link link--info" href="https://<?= $model->info_web_page ?>" target="_blank"><?= $model->info_web_page ?></a>
                </div>
            <?php } ?>
            <?php if ($model->info_email) { ?>
                <div class="col-md-12">
                    <?= Yii::t('pr_page', 'info.email') ?>
                    <a class="link link--info" href="mailto:<?= $model->info_email ?>"><?= $model->info_email ?></a>
                </div>
            <?php } ?>
            <?php if ($model->info_phone) { ?>
                <div class="col-md-12">
                    <?= Yii::t('pr_page', 'info.phone') ?>
                    <a class="link link--info" href="tel:<?= $model->info_phone ?>"><?= $model->info_phone ?></a>
                </div>
            <?php } ?>
            <?php if ($model->info_address) { ?>
                <div class="col-md-12">
                    <?= Yii::t('pr_page', 'info.address') ?>
                    <a class="link link--info" href="https://maps.google.com/?q=<?= $model->info_address ?>" target="_blank"><?= $model->info_address ?></a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<div class="hidden--mobile">
    <div class="container">
        <div class="text-center custom-background-color statistics--box" style="--color: <?= $model->custom_color ?>;">
            <div class="row">
                <div class="col-md-3" data-toggle="tooltip" data-placement="top" title="<?= Yii::t('pr_page', 'tooltip.resolved_reports') ?>">
                    <div>
                        <?= Yii::t('pr_page', 'statistic.resolved_reports') ?>
                    </div>
                    <div class="info-box__result">
                        <?= $model->institution->getReportCountByStatus(Report::STATUS_RESOLVED) ?>
                    </div>
                </div>
                <div class="col-md-3" data-toggle="tooltip" data-placement="top" title="<?= Yii::t('pr_page', 'tooltip.response_time') ?>">
                    <div>
                        <?= Yii::t('pr_page', 'statistic.response_time') ?>
                    </div>
                    <div class="info-box__result">
                        <?= $model->institution->getResponseDays() ?> <?= Yii::t('pr_page', 'statistic.response_time.day') ?>
                    </div>
                </div>
                <?php if (Yii::$app->user->isGuest) { ?>
                    <div class="col-md-3" data-toggle="tooltip" data-placement="top" title="<?= Yii::t('pr_page', 'tooltip.in_progress_reports') ?>">
                        <div>
                            <?= Yii::t('pr_page', 'statistic.in_progress_reports') ?>
                        </div>
                        <div class="info-box__result">
                            <?= $model->institution->getReportCountByStatus([
                                Report::STATUS_WAITING_FOR_RESPONSE,
                                Report::STATUS_WAITING_FOR_SOLUTION,
                                Report::STATUS_WAITING_FOR_ANSWER,
                            ]) ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-md-3" data-toggle="tooltip" data-placement="top" title="<?= Yii::t('pr_page', 'tooltip.resolved_users_reports') ?>">
                        <div>
                            <?= Yii::t('pr_page', 'statistic.resolved_users_reports') ?>
                        </div>
                        <div class="info-box__result">
                            <?= $model->institution->getResolvedReportCountByUserId(Yii::$app->user->id) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="hidden--desktop">
    <div class="text-center custom-background-color statistics--box" style="--color: <?= $model->custom_color ?>;">
        <div class="row">
            <div class="col-xs-6">
                <div>
                    <?= Yii::t('pr_page', 'statistic.resolved_reports') ?>
                </div>
            </div>
            <div class="col-xs-6">
                <div>
                    <?= Yii::t('pr_page', 'statistic.response_time') ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6" data-toggle="tooltip" data-placement="top" title="Tooltip">
                <div class="info-box__result">
                    <?= $model->institution->getReportCountByStatus(Report::STATUS_RESOLVED) ?>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="info-box__result">
                    <?= $model->institution->getResponseDays() ?> <?= Yii::t('pr_page', 'statistic.response_time.day') ?>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 1em;">
            <?php if (Yii::$app->user->isGuest) { ?>
                <div class="col-xs-6">
                    <div>
                        <?= Yii::t('pr_page', 'statistic.in_progress_reports') ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-xs-6">
                    <div>
                        <?= Yii::t('pr_page', 'statistic.resolved_users_reports') ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <?php if (Yii::$app->user->isGuest) { ?>
                <div class="col-xs-6">
                    <div class="info-box__result">
                        <?= $model->institution->getReportCountByStatus([
                            Report::STATUS_WAITING_FOR_RESPONSE,
                            Report::STATUS_WAITING_FOR_SOLUTION,
                            Report::STATUS_WAITING_FOR_ANSWER,
                        ]) ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-xs-6">
                    <div class="info-box__result">
                        <?= $model->institution->getResolvedReportCountByUserId(Yii::$app->user->id) ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="text-left">
        <h1 class="heading custom-color" style="--color: <?= $model->custom_color ?>;">
            <?= Yii::t('pr_page', 'map.title.' . $model->map_status) ?>
        </h1>
        <div class="reportsonmap">
            <div class="row">
                <div class="col-xs-12 col-lg-5"
                     id="map-report-list">
                    <?php
                    foreach ($dataProvider->getModels() as $report) {
                        echo $this->render('../report/_card', [
                            'report' => $report,
                            'showLatLngAsData' => true,
                            'wideOnMobile' => true,
                        ]);
                    }
                    ?>
                </div>
                <div class="col-xs-12 col-lg-7 first-xs last-lg">
                    <div class="reportsonmap__map">
                        <div class="reportsonmap__map__media"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-5">
                    <div class="pagination">
                        <?= app\components\LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (count($news) > 0): ?>
<div class="section--grey section--news">
    <div class="container">
        <h1 class="heading custom-color" style="--color: <?= $model->custom_color ?>;"><?= Yii::t('pr_page', 'title.news') ?></h1>
        <ul class="list list--cards row">
        <?php foreach ($news as $item): ?>
            <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
                <?= $this->render('_card', [
                    'item' => $item,
                    'model' => $model,
                ]); ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php if ($model->video_url || $model->social_feed_url) { ?>
    <div class="container">
        <div class="row section--social-footer">
            <div class="col-md-6 col-xs-12">
                <?php if ($model->video_url) { ?>
                    <div class="support-box__media">
                        <div class="video-wrapper">
                            <iframe class="support-box_video" frameborder="0" height="100%" width="100%" src="<?= str_replace('watch?v=', 'embed/', $model->video_url) ?>?showinfo=0">
                            </iframe>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="hidden--mobile">
                        <aside class="hero hero--bottom">
                            <div class="container">
                                <h2 class="heading heading--1 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
                                <p class="hero__lead col--centered">
                                    <?= Yii::t('app', 'hero.lead'); ?>
                                </p>

                                <div class="hero__button">
                                    <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
                                </div>
                            </div>
                        </aside>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-6  col-xs-12">
                <?php if ($model->social_feed_url) { ?>
                    <?php if (strpos($model->social_feed_url, 'facebook')) { ?>
                        <div class="text-center text-lg-right">
                            <div class="hidden--mobile">
                                <div class="fb-page facebook-feed" data-href="<?= $model->social_feed_url ?>"
                                     data-tabs="timeline"
                                     data-width="500"
                                     data-height="442"
                                     data-small-header="false"
                                     data-adapt-container-width="true"
                                     data-hide-cover="false"
                                     data-show-facepile="true">
                                    <blockquote cite="https://www.facebook.com/facebook" class="fb-xfbml-parse-ignore">
                                        <a href="https://www.facebook.com/facebook">Facebook</a>
                                    </blockquote>
                                </div>
                            </div>
                            <div class="hidden--desktop">
                                <div class="fb-page facebook-feed" data-href="<?= $model->social_feed_url ?>"
                                     data-tabs="timeline"
                                     data-width="500"
                                     data-height="342"
                                     data-small-header="false"
                                     data-adapt-container-width="true"
                                     data-hide-cover="false"
                                     data-show-facepile="true">
                                    <blockquote cite="https://www.facebook.com/facebook" class="fb-xfbml-parse-ignore">
                                        <a href="https://www.facebook.com/facebook">Facebook</a>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    <?php } elseif (strpos($model->social_feed_url, 'twitter')) { ?>
                        <div class="text-center text-lg-right">
                            <div class="hidden--mobile">
                                <a class="text-center twitter-timeline twitter-feed"
                                   data-theme="light"
                                   data-chrome="nofooter"
                                   data-width="500"
                                   data-height="442"
                                   href="<?= $model->social_feed_url ?>">
                                </a>
                            </div>
                            <div class="hidden--desktop">
                                <a class="text-center twitter-timeline twitter-feed"
                                   data-theme="light"
                                   data-chrome="nofooter"
                                   data-width="500"
                                   data-height="342"
                                   href="<?= $model->social_feed_url ?>">
                                </a>
                            </div>
                        </div>
                    <?php } elseif (strpos($model->social_feed_url, 'instagram')) { ?>
                            <div class="instagram-feed">
                                <blockquote class="instagram-media" style="width: 100%;"
                                    data-instgrm-captioned data-instgrm-permalink="<?= explode('?taken-by=', $model->social_feed_url)[0]?>">
                                </blockquote>
                            </div>
                    <?php } ?>
                <?php } else {?>
                    <div class="hidden--mobile">
                        <aside class="hero hero--bottom">
                            <div class="container">
                                <h2 class="heading heading--1 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
                                <p class="hero__lead col--centered">
                                    <?= Yii::t('app', 'hero.lead'); ?>
                                </p>
                                <div class="hero__button">
                                    <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
                                </div>
                            </div>
                        </aside>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
