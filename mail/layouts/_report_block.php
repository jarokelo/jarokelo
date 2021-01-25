<?php

use app\models\db\ReportAttachment;
use app\models\db\Report;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $report Report */

$url = Report::getPictureUrl($report->id, ReportAttachment::SIZE_PICTURE_MEDIUM, true);
$pictureUrl = Report::preparePictureUrl($url);
?>

<table class="row card collapse" style="border:1px solid #EEE;border-collapse:collapse;border-radius:5px;border-spacing:0;margin-bottom:45px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="card__header small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left"><img class="card__img" src="<?= $message->embed($pictureUrl) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:100%"></th>
                                            <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="card__body small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;padding-top:20px;text-align:left;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <table class="row card__title" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                <tr style="padding:0;text-align:left;vertical-align:top">
                                                                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-left:20px;text-align:left"><?= $report->name ?></th>
                                                                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:1.3;margin:0;padding:0!important;padding-left:20px;text-align:left;visibility:hidden;width:0"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr></tbody></table>
                                                <table class="row card__subtitle" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;text-transform:uppercase;width:100%">
                                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                <tr style="padding:0;text-align:left;vertical-align:top">
                                                                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:20px;text-align:left;text-transform:uppercase"><?= $report->getUniqueName() ?></th>
                                                                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-left:20px;text-align:left;text-transform:uppercase;visibility:hidden;width:0"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr></tbody></table>
                                                <table class="row card__description" style="border-collapse:collapse;border-spacing:0;margin-bottom:10px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                <tr style="padding:0;text-align:left;vertical-align:top">
                                                                    <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left">
                                                                        <table class="row card__line" style="border-collapse:collapse;border-spacing:0;margin-bottom:15px;margin-left:20px;margin-top:15px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                                                                            <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left"><img src="<?= $message->embed(Yii::getAlias('@mailImages/line-blue.png')) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto"></th>
                                                                                            <th class="expander" style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr></tbody></table>
                                                                        <table class="row card__description-item" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                                                                            <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:20px;padding-right:20px;text-align:left">Beküldés ideje: <span class="dark" style="color:#565656"><?= Yii::$app->formatter->asDatetime($report->created_at) ?></span></th>
                                                                                            <th class="expander" style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-left:20px;padding-right:20px;text-align:left;visibility:hidden;width:0"></th>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr></tbody></table>
                                                                        <table class="row card__description-item" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                                                                            <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:20px;padding-right:20px;text-align:left">Beküldte: <span class="dark" style="color:#565656"><?= $report->anonymous ? Yii::t('data', 'report.anonymous') : $report->user->getFullName() ?></span></th>
                                                                                            <th class="expander" style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-left:20px;padding-right:20px;text-align:left;visibility:hidden;width:0"></th>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr></tbody></table>
                                                                        <table class="row card__description-item" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                                                                            <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:20px;padding-right:20px;text-align:left">Helyszín: <span class="dark" style="color:#565656"><?= $report->user_location ?></span></th>
                                                                                            <th class="expander" style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-left:20px;padding-right:20px;text-align:left;visibility:hidden;width:0"></th>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr></tbody></table>
                                                                        <?php if ($report->institution_id): ?>
                                                                        <table class="row card__description-item" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                                                                            <th style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:20px;padding-right:20px;text-align:left">Illetékes: <span class="dark" style="color:#565656"><?= $report->institution->name ?></span></th>
                                                                                            <th class="expander" style="Margin:0;color:#A0A0A0;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-left:20px;padding-right:20px;text-align:left;visibility:hidden;width:0"></th>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr></tbody></table>
                                                                        <?php endif; ?>
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr></tbody></table>
                                                <?php if ($report->status !== \app\models\db\Report::STATUS_DELETED): ?>
                                                <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                                        <th class="btn btn--green btn--wide small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:10px;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                                            <table style="background:#9BD158;border-collapse:collapse;border-radius:65px;border-spacing:0;margin:15px auto 15px auto;padding:0;text-align:left;vertical-align:top;width:auto">
                                                                <tr style="padding:0;text-align:left;vertical-align:top">
                                                                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;width:300px">
                                                                        <a href="<?= $report->getUrl() ?>" style="Margin:0;color:#fff;display:block;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-bottom:13px;padding-left:10px;padding-right:10px;padding-top:12px;text-align:center;text-decoration:none">Megnézem a bejelentést</a>
                                                                    </th>
                                                                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr></tbody></table>
                                                <?php endif; ?>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
