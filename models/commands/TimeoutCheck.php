<?php

namespace app\models\commands;

use Yii;
use app\models\db\ActivityCache;
use app\models\db\AdminCity;
use app\models\db\CronLog;
use app\models\db\Report;
use app\models\db\ReportActivity;

/**
 * Responsible cron to create tasks of outdated activities
 * Able to close reports and construct new activity
 */
class TimeoutCheck
{
    /**
     * @var int
     */
    const ONE_DAY_IN_SEC = 86400;

    /**
     * @var string
     */
    const DATE_FROM = '2020-01-01';

    /**
     * @static
     */
    public static function process()
    {
        $log = new CronLog(['type' => CronLog::TYPE_TIMEOUT_CHECK]);
        $log->addOutput('Start TimeoutCheck Job');

        /** @var ReportActivity[] $activities */
        $activities = ReportActivity::find()
            ->where([
                'AND',
                ['report_activity.is_latest' => 1],
                ['report_activity.type' => ReportActivity::TYPE_SEND_TO_AUTHORITY],
                ['>=', 'report_activity.created_at', strtotime(self::DATE_FROM)],
                ['>', 'report.sent_email_count', 0],
                ['<=', 'report_activity.created_at', time() - Yii::$app->params['answerWaitDays'] * self::ONE_DAY_IN_SEC],
            ])
            ->leftJoin(Report::tableName(), 'report.id=report_activity.report_id')
            ->all();

        if (!$activities) {
            $log->addOutput('No activities found with this conditions!', true);
            return;
        }

        // Accumulating type `no_answer` activities.
        $activityList = [];

        foreach ($activities as $activity) {
            $log->addOutput(
                sprintf(
                    'Start processing activity#%s of report#%s!',
                    $activity->id,
                    $activity->report_id
                )
            );

            if ($activity->type == ReportActivity::TYPE_SEND_TO_AUTHORITY) {
                $log->addOutput(' - activity type is SEND_TO_AUTHORITY');

                if ($activity->report->sent_email_count == 1) {
                    $log->addOutput(' - 1 email already sent. Create NO_ANSWER activity.');

                    $entity = self::prepareActivity(
                        ReportActivity::TYPE_NO_ANSWER,
                        $activity
                    );
                    $activityList[] = [
                        'id' => $entity->id,
                        'city_id' => $entity->institution->city_id,
                    ];
                } elseif (
                    $activity->report->sent_email_count >= 2
                    && $activity->created_at <= time()
                    - (Yii::$app->params['answerWaitDays'] + Yii::$app->params['answerWaitDaysAfterResend'])
                    * self::ONE_DAY_IN_SEC
                ) {
                    $log->addOutput(
                        ' - more than 2 emails sent and it is over than the waiting days. '
                        . 'Close report with NO_ANSWER.'
                    );
                    $activity->report->close(
                        Report::CLOSE_REASON_NO_ANSWER,
                        ''
                    );
                } else {
                    $log->addOutput(' - no action.');
                }
            }
        }

        $log->save();
        self::createCache($activityList);
    }

    /**
     * Creating activity cache entries where activity type is `no_answer`
     * for privileged administrators
     *
     * @param array $activityList
     */
    private static function createCache(array $activityList)
    {
        $adminCityMap = [];

        /** @var AdminCity $adminCity */
        foreach (AdminCity::find()->all() as $adminCity) {
            $adminCityMap[$adminCity->city_id][] = $adminCity->admin_id;
        }

        $entities = [];

        foreach ($activityList as $activity) {
            if (isset($adminCityMap[$activity['city_id']])) {
                foreach ($adminCityMap[$activity['city_id']] as $admin) {
                    $entities[] = [
                        'report_activity_id' => $activity['id'],
                        'admin_id' => $admin,
                        'created_at' => time(),
                    ];
                }
            }
        }

        if (!$entities) {
            return;
        }

        // Bulk insert.
        Yii::$app->db->createCommand()->batchInsert(
            ActivityCache::tableName(),
            [
                'report_activity_id',
                'admin_id',
                'created_at',
            ],
            $entities
        )->execute();
    }

    /**
     * @param string $type
     * @param ReportActivity $activity
     * @return ReportActivity
     * @static
     */
    private static function prepareActivity($type, ReportActivity $activity)
    {
        $data = [];

        foreach ($activity as $property => $value) {
            switch ($property) {
                case 'id':
                    // unset id
                    $value = null;
                    break;
                case 'type':
                    // should be added because array merge will override the first param
                    // of addActivity below
                    $value = $type;
                    break;
            }

            $data[$property] = $value;
        }

        // pass report activity properties to new activity
        return $activity->report->createActivity($type, $data);
    }
}
