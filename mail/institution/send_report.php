<?php

use app\components\helpers\Link;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\db\Report;
use app\models\db\ReportAttachment;

/**
 * @var app\models\db\Report $report
 * @var app\models\db\Institution $institution
 * @var string $name name of the actual contact at the institution
 * @var yii\web\View $this
 * @var yii\swiftmailer\Message $message
 */

$url = Report::getPictureUrl($report->id, ReportAttachment::SIZE_PICTURE_MEDIUM, true);
$pictureUrl = Report::preparePictureUrl($url);
?>

<!-- start content (sikeres illetekeseknek) -->
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="title title--main small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-bottom:15px;text-align:left">Levél tartalom</th>
                                            <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0!important;padding-bottom:15px;text-align:left;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<!-- end content (sikeres illetekeseknek) -->
