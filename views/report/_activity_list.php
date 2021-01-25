<?php

use app\assets\AppAsset;
use app\components\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\helpers\Link;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */
/* @var \app\models\db\ReportActivity $activity */
/* @var bool $showDonationBox */

Pjax::begin([
    'id' => 'report-activity-list',
    'linkSelector' => false,
    'formSelector' => '#report-comment-form',
]);
$assetUrl = AppAsset::register($this)->baseUrl;

?>
    <ul class="list list--comments">
        <?php foreach ($model->reportActivities as $activity): ?>
            <?php if ($activity->isNotVisible()) {
                continue;
            }

            echo $this->render('_activity_block', [
                'model' => $activity,
                'report' => $model,
            ]);
        endforeach; ?>
    </ul>
<?php

Pjax::end();

if (!empty($showDonationBox)): ?>
    <div class="comment">
        <img src="<?= Yii::getAlias('@web/images/default.png') ?>" class="comment__media">
        <div class="comment__body section--orange">
            <div class="comment__body--head">
                <p class="comment__body--paragraph uppercase"><?= Yii::t('app', 'hero.h1') ?></p>
                <p class="comment__body--paragraph"><?= Yii::t('app', 'cta.text') ?></p>
            </div>
            <div class="comment__body--body">
                <div class="donation-container-cta uppercase hero-footer center">
                    <a href="<?= Link::to(
                        [
                            Link::ABOUT,
                            Link::POSTFIX_ABOUT_SUPPORT,
                        ]
                    ) ?>" class="button button--small button--danger button-cta-donation-help">
                        <?= Yii::t('app', 'hero.support') ?>
                    </a>
                </div>

                <div class="container-fluid top-progress-container" style="margin: 0 10px 0 10px; padding-bottom: 20px;">
                    <div class="progress-main-container">
                        <div class="progressbar hidden--mobile"></div>
                        <div class="progress_container">
                            <div class="progress_people"
                                 style="background-image:url('<?= $assetUrl ?>/images/people.png');"></div>
                            <div class="progress_triangle"
                                 style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        </div>

                        <div class="money_step money_step_left">0 millió Ft</div>
                        <div class="money_step money_step_center">3 millió Ft</div>
                        <div class="money_step money_step_right">6 millió Ft</div>

                        <div class="progress_triangle small_triangle small_triangle_left"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        <div class="progress_triangle small_triangle small_triangle_center"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        <div class="progress_triangle small_triangle small_triangle_right"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>

                    </div>
                </div>

                <div class="container-fluid mobile-container">
                    <div class="progress-main-container progress-main-container_small">
                        <div class="progressbar"></div>
                        <div class="progress_container">
                            <div class="progress_people"
                                 style="background-image:url('<?= $assetUrl ?>/images/people.png');"></div>
                            <div class="progress_triangle"
                                 style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        </div>

                        <div class="money_step money_step_left">0 millió Ft</div>
                        <div class="money_step money_step_center">3 millió Ft</div>
                        <div class="money_step money_step_right">6 millió Ft</div>

                        <div class="progress_triangle small_triangle small_triangle_left"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        <div class="progress_triangle small_triangle small_triangle_center"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                        <div class="progress_triangle small_triangle small_triangle_right"
                             style="background-image:url('<?= $assetUrl ?>/images/triangle.png');"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comment__body--head {
            font-size: 28px;
            color: #000;
            text-align: center;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .comment__body--paragraph {
            margin: 0;
            font-weight: 700;
            line-height: 1.1em;
        }

        .button-cta-donation-help {
            color: #fff;
        }

        .button-cta-donation-help:hover {
            background-color: #9bd159;
        }

        .donation-container-cta {
            margin: 15px 15px 60px 15px;
        }

        .money_step {
            position: absolute;
            width: 150px;
            text-align: center;
            margin-left: -75px;
            font-size: 30px;
        }

        .progress_triangle {
            height: 130px;
            width: 89px;
            left: 2px;
            background-repeat: no-repeat;
            top: 78px;
            position: absolute;
        }

        .small_triangle {
            width: 33px;
            height: 49px;
            top: 25px;
            background-size: contain;
        }

        .small_triangle_left {
            left: 10px;
        }

        .small_triangle_center {
            left: 50%;
            margin-left: -16px
        }

        .small_triangle_right {
            left: 100%;
            margin-left: -43px
        }

        .progress-main-container {
            margin-top: 30px;
        }

        .progress-main-container_small {
            margin:70px 30px 90px 30px !important;
        }

        .progress-main-container_small .money_step {
            font-size: 13px;
        }

        .progress-main-container_small .money_step_left {
            text-align: left !important;
            margin-left: -23px;
        }

        .progress-main-container_small .money_step_right {
            text-align: right !important;
            margin-left: -127px;
        }

        .money_step {
            position: absolute;
            width: 150px;
            text-align: center;
            margin-left: -75px;
            font-size: 10px;
        }

        .money_step_center {
            left: 50%;
        }

        .money_step_right {
            right: -40px;
        }

        .money_step_left {
            text-align: right;
            margin-left: -95px;
        }

        .progress_container {
            transition: none;
        }

        @media (max-width: 420px) {
            .progress_triangle {
                font-size: 12px;
            }
        }

        @media (max-width: 1089px) {
            .money_step_right {
                right: -5px;
            }
        }

        @media (max-width: 397px) {
            .comment__body--head {
                font-size: 24px;
            }

            .money_step_right {
                right: -29px;
            }
        }

        .container-fluid.mobile-container {
            padding-bottom: 1px;
        }

        .comment__body.section--orange {
            height: 330px;
        }
    </style>
<?php endif;
$this->render(
    '/_snippets/_progress',
    [
        'fix_percentage' => 70,
        'item_width' => 23,
        'item_gap' => 5,
    ]
);