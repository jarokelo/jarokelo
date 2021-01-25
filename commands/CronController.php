<?php

namespace app\commands;

use app\models\commands\DailyMail;
use app\models\commands\DownloadEmails;
use app\models\commands\Notifications;
use app\models\commands\TimeoutCheck;
use app\models\commands\UploadGarbageCollector;
use app\models\db\Report;
use app\models\db\User;
use yii\console\Controller;

/**
 * Handles CRON related actions.
 *
 * @package app\commands
 */
class CronController extends Controller
{
    /**
     * Runs every task.
     */
    public function actionIndex()
    {
        $this->actionDownloadEmails();
        $this->actionMapEmailsWithNewContacts();
        $this->actionNotification();
        $this->actionTimeoutCheck();
        $this->actionSendDailySummaryMails();
        $this->actionGarbageCollector();
    }

    /**
     * Sets a report from 'waiting for solution' to 'waiting for response' when the
     * institutions deadline for resolving the report expires and the institution fails
     * to inform us about the resolution status. This triggers a mail to
     * the reporter so he/she can give a feedback.
     */
    public function actionNotification()
    {
        Notifications::process();
    }

    /**
     * Downloads new e-mails.
     */
    public function actionDownloadEmails()
    {
        DownloadEmails::process();
    }

    /**
     * Checks, if a User failed to response in time.
     */
    public function actionTimeoutCheck()
    {
        TimeoutCheck::process();
    }

    public function actionSendDailySummaryMails()
    {
        DailyMail::process();
    }

    /**
     * Store the user ranks query results to cache for 1 day, others for 10 minutes
     */
    public function actionCacheUserRanks()
    {
        User::getRanks();
        User::getCurrentMonthRanks();
        User::countActive();
        Report::countResolved();
        Report::countUnresolved();
    }

    /**
     * Removes old, unused images from runtime/upload-tmp folder
     */
    public function actionGarbageCollector()
    {
        $gc = new UploadGarbageCollector();
        $gc->process();
    }

    public function actionMapEmailsWithNewContacts()
    {
        DownloadEmails::mapEmailsWithNewContacts();
    }
}
