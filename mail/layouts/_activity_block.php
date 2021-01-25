<?php

use app\models\db\ReportActivity;
use app\models\db\Report;
use app\models\db\ReportAttachment;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $reportActivity */
/* @var $baseMessage \yii\mail\BaseMessage instance of newly created mail message */
/* @var $lastItem boolean true if it's the last report activity in the digest list */

$url = Report::getPictureUrl($reportActivity->report_id, ReportAttachment::SIZE_PICTURE_MEDIUM, true);
$pictureUrl = Report::preparePictureUrl($url);
?>

<?php if (in_array($reportActivity->type, [ReportActivity::TYPE_ANSWER, ReportActivity::TYPE_COMMENT, ReportActivity::TYPE_MOD_STATUS])): ?>
    <?php if (!$lastItem): ?>
        <table class="row list__item" style="border-bottom:1px solid #F0F0F0;border-collapse:collapse;border-spacing:0;margin-bottom:20px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <?php else: ?>
        <table class="row list__item list__item--last" style="border:0!important;border-bottom:1px solid #F0F0F0;border-collapse:collapse;border-spacing:0;margin-bottom:20px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <?php endif; ?>

        <tbody><tr style="padding:0;text-align:left;vertical-align:top">
            <th class="list__img-wrapper small-4 large-2 columns first" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:16.66667%">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-right:20px;text-align:left">
                            <img src="<?= $baseMessage->embed($pictureUrl) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;margin-bottom:20px;max-width:100%;outline:0;text-decoration:none;width:auto">
                        </th>
                    </tr>
                </table>
            </th>
            <th class="small-8 large-10 columns last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:83.33333%">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:0;margin-top:0;padding:0;text-align:left">
                                <?= $activityText ?>
                            </p>
                            <p class="list__meta" style="Margin:0;Margin-bottom:10px;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;margin-bottom:0;margin-top:0;padding:0;padding-bottom:20px;text-align:left"><?= Yii::$app->formatter->asDatetime($reportActivity->created_at) ?></p>
                        </th>
                    </tr>
                </table>
            </th>
        </tr>
        </tbody>
    </table>
<?php endif; ?>
