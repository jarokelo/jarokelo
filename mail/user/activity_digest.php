<?php
use app\models\db\ReportActivity;
use yii\helpers\Url;
use app\components\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $name string name of the user */
/* @var $date string digest date */
/* @var $own_report_activities \app\models\db\ReportActivity[] */
/* @var $subscribed_report_activities \app\models\db\ReportActivity[] */

$typeDisplayData = ReportActivity::typeDisplayData();
?>

<!-- start content (napi-osszefoglalo) -->
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="title title--main small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-bottom:15px;text-align:left"><?= Yii::t('email', 'hello_with_name', ['name' => $name]) ?></th>
                                            <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0!important;padding-bottom:15px;text-align:left;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left">Az alábbi aktivitások történtek a bejelentéseiddel  <br> kapcsolatban <strong><?= Yii::$app->formatter->asDate($date, 'php:mm d.') ?></strong>:</p>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="title title--secondary small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-bottom:20px;text-align:left">Saját bejelentéseim</th>
                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-bottom:20px;text-align:left;visibility:hidden;width:0"></th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<?php if (!empty($own_report_activities)): ?>
    <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
            <th class="list list--bordered small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <?php
                            foreach ($own_report_activities as $i => $own_report_activity) {
                                if ($own_report_activity->isNotVisible()) {
                                    continue;
                                }

                                $reportLink = '<a href=' . Url::to(['report/view', 'id' => $own_report_activity->report_id], true) . '>' . Html::encode($own_report_activity->report->name) . '</a>';

                                switch ($own_report_activity->type) {
                                    case ReportActivity::TYPE_ANSWER:
                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.response',
                                            [
                                                'institutionName' => $own_report_activity->institution->name,
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    case ReportActivity::TYPE_COMMENT:
                                        $report = $own_report_activity->report;

                                        if ($report->anonymous && $own_report_activity->user->id === $report->user_id) {
                                            $fullName = Yii::t('data', 'report.anonymous');
                                        } else {
                                            $fullName = $own_report_activity->user->getFullName();
                                        }

                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.new-report-comment-owned',
                                            [
                                                'fullName' => $fullName,
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    case ReportActivity::TYPE_MOD_STATUS:
                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.changed-status',
                                            [
                                                'newStatus' => \app\models\db\Report::statuses()[$own_report_activity->report->status],
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    default:
                                        $activityText = '';
                                        break;
                                }
                                echo $this->render('@app/mail/layouts/_activity_block', [
                                    'reportActivity' => $own_report_activity,
                                    'baseMessage' => $message,
                                    'activityText' => $activityText,
                                    'lastItem' => ($i + 1 == count($own_report_activities)),
                                ]);
                            }
                            ?>
                        </th>
                    </tr>
                </table>
            </th>
        </tr></tbody></table>
<?php endif; ?>
<?php if (!empty($subscribed_report_activities)): ?>
    <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
            <th class="title title--secondary small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-bottom:20px;text-align:left">Követett bejelentéseim</th>
                        <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;padding-bottom:20px;text-align:left;visibility:hidden;width:0"></th>
                    </tr>
                </table>
            </th>
        </tr></tbody></table>
    <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
            <th class="list list--bordered small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <?php
                            foreach ($subscribed_report_activities as $i => $subscribed_report_activity) {
                                if ($subscribed_report_activity->isNotVisible()) {
                                    continue;
                                }

                                $reportLink = '<a href=' . Url::to(['report/view', 'id' => $subscribed_report_activity->report_id], true) . '>' . $subscribed_report_activity->name . '</a>';
                                switch ($subscribed_report_activity->type) {
                                    case ReportActivity::TYPE_ANSWER:
                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.response',
                                            [
                                                'institutionName' => $subscribed_report_activity->institution->name,
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    case ReportActivity::TYPE_COMMENT:
                                        $report = $subscribed_report_activity->report;

                                        if ($report->anonymous && $subscribed_report_activity->user->id === $report->user_id) {
                                            $fullName = Yii::t('data', 'report.anonymous');
                                        } else {
                                            $fullName = $subscribed_report_activity->user->getFullName();
                                        }

                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.new-report-comment-followed',
                                            [
                                                'fullName' => $fullName,
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    case ReportActivity::TYPE_MOD_STATUS:
                                        $activityText = Yii::t(
                                            'email',
                                            'content.digest.changed-status',
                                            [
                                                'newStatus' => \app\models\db\Report::statuses()[$subscribed_report_activity->report->status],
                                                'reportLink' => $reportLink,
                                            ]
                                        );
                                        break;
                                    default:
                                        $activityText = '';
                                        break;
                                }
                                echo $this->render('@app/mail/layouts/_activity_block', [
                                    'reportActivity' => $subscribed_report_activity,
                                    'baseMessage' => $message,
                                    'activityText' => $activityText,
                                    'lastItem' => ($i + 1 == count($subscribed_report_activities)),
                                ]);
                            }
                            ?>
                        </th>
                    </tr>
                </table>
            </th>
        </tr></tbody></table>
<?php endif; ?>
<!-- end content (napi-osszefoglalo) -->
