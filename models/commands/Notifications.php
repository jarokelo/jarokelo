<?php

namespace app\models\commands;

use app\models\db\CronLog;
use app\models\db\Notification;
use app\models\db\Report;
use app\models\db\ReportActivity;
use yii\helpers\VarDumper;

class Notifications
{
    public static function process()
    {
        $log = new CronLog(['type' => CronLog::TYPE_NOTIFICATION]);
        $log->addOutput("Start processing notifications\n");

        /* @var \app\models\db\Notification[] $notifications */
        $notifications = Notification::find()
            ->where(['status' => Notification::STATUS_WAITING])
            ->andWhere(['sent_date' => null])
            ->andWhere(['<=', 'send_date', time()])
            ->with(['user', 'report'])
            ->all();

        if (empty($notifications)) {
            $log->addOutput("No notifications found\n", 1);
            return;
        }

        foreach ($notifications as $notification) {
            $log->addOutput("Start processing notification#{$notification->id} of Report#{$notification->report_id}\n");
            // if the report status is not 'waiting for solution' anymore cancel the notification
            if ($notification->report->status != Report::STATUS_WAITING_FOR_SOLUTION) {
                $notification->status = Notification::STATUS_CANCELLED;
                $notification->save();
                $log->addOutput(" - Report is no longer in WAITING_FOR_SOLUTION state. skipping...\n");

                continue;
            }

            // set the status to 'waiting for response'
            $notification->report->status = Report::STATUS_WAITING_FOR_RESPONSE;
            if (!$notification->report->save()) {
                $log->addErrorMessage(' - Unable to update Report! Errors: ' . VarDumper::dumpAsString($notification->report->getErrors()));
                continue;
            }
            $log->addOutput(" - Report status changed to WAITING_FOR_RESPONSE state\n");

            // add a ReportActivity to log what happened and also trigger the notification mail to the reporter
            if (!$notification->report->addActivity(ReportActivity::TYPE_GET_USER_RESPONSE, [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
            ])) {
                $log->addErrorMessage(" - Unable to create GET_USER_RESPONSE activity for this notification\n");
                continue;
            }
            $log->addOutput(" - GET_USER_RESPONSE activity successfully created, email sent to user\n");

            // if anything went fine, set the notifications status to 'sent'
            $notification->sent_date = time();
            $notification->status = Notification::STATUS_SENT;
            if (!$notification->save()) {
                $log->addErrorMessage('Unable to update Notification! Errors: ' . VarDumper::dumpAsString($notification->getErrors()));
                continue;
            }
        }

        $log->save();
    }
}
