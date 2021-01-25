<?php

namespace app\models\commands;

use Yii;
use app\models\db\CronLog;
use app\models\db\MailLog;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\User;
use yii\db\ActiveQuery;
use yii\db\Expression;

class DailyMail
{
    const DATE_YMD = 'date_ymd';
    const DATE_TIMESTAMP_START = 'timestamp_start';
    const DATE_TIMESTAMP_END = 'timestamp_end';

    /**
     * should not be a high number because it could run for long time
     */
    const NUMBER_OF_USERS_TO_SEND_PER_JOB = 100;

    /**
     * 24h
     */
    const SEND_MAILS_AFTER_TIME = '08:00';

    private static function isTimeToSendMails()
    {
        return date('H:i') > self::SEND_MAILS_AFTER_TIME;
    }

    /**
     * Sends daily summary email to users about the activities of previous day
     *
     * @param null $date if null, the date will be the last day
     */
    public static function process($date = null)
    {
        $log = new CronLog(['type' => CronLog::TYPE_DAILY_MAIL]);
        $log->addOutput('Daily mail - process was started');

        try {
            if (self::isTimeToSendMails() === false) {
                return;
            }

            $date = self::getDate($date, self::DATE_YMD);
            $users = self::getUsersToNotifyAboutActivities($date);

            /** @var User $user */
            foreach ($users as $user) {
                if (!$user->isNotificationAllowed(User::NOTIFICATION_DAILY)) {
                    self::markSent($date, $user);
                    continue;
                }
                $ownReports = $user->isNotificationAllowed(User::NOTIFICATION_TYPE_OWNED) ? $user->getReports()->all() : [];
                $followedReports = $user->isNotificationAllowed(User::NOTIFICATION_TYPE_FOLLOWED) ? $user->getFollowedReports()->all() : [];

                $ownReportActivities = self::filterActivitiesToSend($ownReports, $user, $date);
                $followedReportActivities = self::filterActivitiesToSend($followedReports, $user, $date);

                self::sendMail($ownReportActivities, $followedReportActivities, $user, $date);
            }
        } catch (\Exception $e) {
            $log
                ->addErrorMessage(
                    sprintf(
                        'An error happened during daily mail process %s',
                        $e
                    )
                );
        }

        $log->addOutput('Daily mail - process was completed');
        $log->save();
    }

    private static function getUsersToNotifyAboutActivities($date)
    {
        $userIdsReceivedToday = MailLog::find()
            ->select('user_id')
            ->where([
                'type' => MailLog::TYPE_DAILY,
                'type_info' => $date,
            ])->column();

        // owned reports with activities
        $userIdsWithOwnReportActivities = User::find()
            ->select('user.id')
            ->distinct()
            ->join('JOIN', Report::tableName(), Report::tableName() . '.user_id=' . User::tableName() . '.id')
            ->join('JOIN', ReportActivity::tableName(), ReportActivity::tableName() . '.report_id = ' . Report::tableName() . '.id')
            ->where('IFNULL(' . User::tableName() . '.id, 0) <> IFNULL(' . ReportActivity::tableName() . '.user_id, 0)')
            ->andWhere([
                'BETWEEN',
                ReportActivity::tableName() . '.created_at',
                self::getDate($date, self::DATE_TIMESTAMP_START),
                self::getDate($date, self::DATE_TIMESTAMP_END),
            ])->column();

        // following reports with activities
        $userIdsWithFollowingReportActivities = User::find()
            ->select('user.id')
            ->distinct()
            ->join('JOIN', 'report_following', 'report_following.user_id=' . User::tableName() . '.id')
            ->join('JOIN', Report::tableName(), Report::tableName() . '.id=report_following.report_id')
            ->join('JOIN', ReportActivity::tableName(), ReportActivity::tableName() . '.report_id=' . Report::tableName() . '.id')
            ->where('IFNULL(' . User::tableName() . '.id, 0) <> IFNULL(' . ReportActivity::tableName() . '.user_id, 0)')
            ->andWhere('IFNULL(' . User::tableName() . '.id, 0) <> IFNULL(' . Report::tableName() . '.user_id, 0)')
            ->andWhere([
                'BETWEEN',
                'report_activity.created_at',
                self::getDate($date, self::DATE_TIMESTAMP_START),
                self::getDate($date, self::DATE_TIMESTAMP_END),
            ])->column();

        return User::find()
            ->where([
                'id' => array_merge($userIdsWithOwnReportActivities, $userIdsWithFollowingReportActivities),
            ])
            ->andFilterWhere(['NOT', ['id' => $userIdsReceivedToday]])
            ->limit(self::NUMBER_OF_USERS_TO_SEND_PER_JOB > 100 ? 100 : self::NUMBER_OF_USERS_TO_SEND_PER_JOB)
            ->all();
    }

    /**
     * @param $reports
     * @param User $user
     * @param $date
     * @return array
     */
    private static function filterActivitiesToSend($reports, $user, $date)
    {
        $filteredActivities = [];
        /** @var Report $report */
        foreach ($reports as $report) {
            /** @var ActiveQuery $activities [] */
            $activities = $report->getReportActivities()
                ->with(['report'])
                ->andWhere(new Expression('IFNULL(report_activity.user_id ,0) <> :userId', [':userId' => $user->id]))
                ->andWhere([
                    'BETWEEN',
                    'report_activity.created_at',
                    self::getDate($date, self::DATE_TIMESTAMP_START),
                    self::getDate($date, self::DATE_TIMESTAMP_END),
                ]);

            /** @var ReportActivity $reportActivity */
            foreach ($activities->all() as $reportActivity) {
                if ($reportActivity->isNotVisible()) {
                    continue;
                }

                $filteredActivities[] = $reportActivity;
            }
        }

        return $filteredActivities;
    }

    /**
     * Returns date in the requested format
     *
     * @param $date
     * @param $type
     * @return bool|int|string
     */
    private static function getDate($date, $type)
    {
        if ($date === null) {
            $date = date('Y-m-d', strtotime('-1day'));
        }

        if (is_integer($date)) {
            $date = date('Y-m-d', $date);
        }

        $timeDayBegins = strtotime('midnight', strtotime($date));
        $timeDayEnds = strtotime('tomorrow', $timeDayBegins) - 1;

        switch ($type) {
            case self::DATE_YMD:
                return $date;
                break;
            case self::DATE_TIMESTAMP_START:
                return $timeDayBegins;
                break;
            case self::DATE_TIMESTAMP_END:
                return $timeDayEnds;
                break;
            default:
                break;
        }
    }

    /**
     * Notifies the User about the activities of the last day
     *
     * @param $ownReportActivities
     * @param $followedReportActivities
     * @param User $user
     * @param $date
     */
    private static function sendMail($ownReportActivities, $followedReportActivities, User $user, $date)
    {
        if ((empty($ownReportActivities) && empty($followedReportActivities)) || $user === null) {
            return;
        }

        Yii::$app->mailer->compose('user/activity_digest', [
            'name' => $user->first_name,
            'date' => $date,
            'own_report_activities' => $ownReportActivities,
            'subscribed_report_activities' => $followedReportActivities,
        ])
            ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
            ->setTo([$user->email => $user->getFullName()])
            ->setSubject(Yii::t('email', 'subject.daily-mail', ['date' => Yii::$app->formatter->asDate($date, 'php:mm d.')]))
            ->send();

        self::markSent($date, $user);
    }

    /**
     * @param $date
     * @param User $user
     */
    private static function markSent($date, $user)
    {
        $log = new MailLog([
            'type' => MailLog::TYPE_DAILY,
            'type_info' => $date,
            'user_id' => $user->id,
        ]);

        $log->save();
    }
}
