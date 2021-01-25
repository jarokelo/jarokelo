<?php

use app\components\helpers\SVG;
use yii\helpers\Html;
use app\models\db\Report;
use \app\assets\AppAsset;

$bundle = AppAsset::register($this);

/* @var \app\models\db\Report $report */
/* @var boolean $showLatLngAsData (optional) */
/* @var boolean $wideOnMobile inserts a wide layout for mobiles (optional) */

$cardPictureUrl = Report::getPictureUrl($report->id, \app\models\db\ReportAttachment::SIZE_PICTURE_THUMBNAIL);
?>

<article class="card <?= (isset($wideOnMobile) && $wideOnMobile === true) ? 'card--wideonmobile' : '' ?>" data-id="<?=$report->id ?>" <?= isset($showLatLngAsData) && $showLatLngAsData === true ? 'data-lat="' . $report->latitude . '" data-lng="' . $report->longitude . '"' : ''?>>
    <div class="row">
        <div class="col-custom-1">
            <figure class="card__media">
                <div class="hidden--mobile">
                    <div class="badge--top-left">
                        <span class="badge badge--<?= Yii::t('const', 'report.class.' . $report->status); ?>"><?= yii\helpers\ArrayHelper::getValue(Report::statuses(), $report->status, $report->status); ?></span>
                        <?php if ($report->isFollowing()): ?>
                            <span class="badge badge--<?= Yii::t('const', 'report.class.' . $report->status); ?>"><?=Yii::t('label', 'generic.following'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?= Html::a($report->getCommentCount(), $report->getUrl(), ['class' => 'badge badge--comment badge--comment--top-right']) ?>
                </div>
                <a class="card__media__bg" target="_blank" href="<?= $report->getUrl() ?>"
                   style="<?= Html::encode('background-image: url("' . $cardPictureUrl . '");')?>">
                    <img src="<?= $cardPictureUrl ?>" alt="">
                </a>
            </figure>
         </div>

        <div class="col-custom-2 card__badgecontainer">
            <div class="hidden--desktop">
                <div class="badge--top-left">
                    <span class="badge badge--<?= Yii::t('const', 'report.class.' . $report->status); ?>"><?= yii\helpers\ArrayHelper::getValue(Report::statuses(), $report->status, $report->status); ?></span>
                    <?php if ($report->isFollowing()): ?>
                        <span class="badge badge--<?= Yii::t('const', 'report.class.' . $report->status); ?>"><?=Yii::t('label', 'generic.following'); ?></span>
                    <?php endif; ?>
                </div>
                <?= Html::a($report->getCommentCount(), $report->getUrl(), ['class' => 'badge badge--comment badge--comment--top-right']) ?>
            </div>

            <h3 class="card__title"><?= Html::a($report->name, $report->getUrl()); ?></h3>

            <footer class="card__footer">
                <div class="card__ellipsis"><span class="card__label"><?= Yii::t('report', 'block.report_time') ?></span> <time datetime="<?= Yii::$app->formatter->asDatetime($report->created_at, 'php:c') ?>"><?= Yii::$app->formatter->asDate($report->created_at); ?></time></div>
                <div class="card__ellipsis"><span class="card__label"><?= Yii::t('report', 'block.reporter'); ?></span> <?= (($report->anonymous || $report->user_id === null) ? Yii::t('report', 'report.anonymous') : Html::a($report->user->fullName, \app\components\helpers\Link::to([\app\components\helpers\Link::PROFILES, $report->user->id]), ['class' => 'link link--black'])) ?></div>
                <address class="card__address hidden--mobile--webkitbox card__ellipsis">
                    <?= SVG::icon(SVG::ICON_POI, ['class' => 'card__address__icon icon icon--large'])?>
                    <?= $report->getLocationName() ?>
                </address>
            </footer>
        </div>
    </div>
</article>
