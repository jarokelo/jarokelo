<?php

namespace app\models\commands;

use app\components\helpers\S3;
use app\components\storage\StorageInterface;
use Yii;
use app\components\EmailHelper;
use app\components\helpers\GmailApi;
use app\components\helpers\GmailEmailWrapper;
use app\components\helpers\Html;
use app\models\db\City;
use app\models\db\Contact;
use app\models\db\CronLog;
use app\models\db\Email;
use app\models\db\GmailEmailToken;
use app\models\db\Institution;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\models\db\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 *
 */
class DownloadEmails extends Model
{
    /**
     * How many emails should be retrieved per read request
     *
     * @var string
     */
    const BATCH_READ_EMAIL_COUNT = 10;

    /**
     * Skip the listed email addresses from the email account
     * For example - skip@me.hu
     *
     * @var array
     */
    protected static $skipFrom = [];

    /**
     *
     */
    public static function process()
    {
        $log = new CronLog(['type' => CronLog::TYPE_DOWNLOAD_EMAILS]);

        try {
            $cities = static::getCitiesWithEmailAccount();

            if (count($cities) === 0) {
                $log->addErrorMessage('No cities found with email account', 1);
                return;
            }

            foreach ($cities as $city) {
                $log->addOutput('Start processing messages for ' . $city['name']);
                $messages = static::getMessagesFromServer($city);

                if (count($messages) === 0) {
                    $log->addOutput(' - no unread messages found');
                }

                foreach ($messages as $message) {
                    $log->addOutput(" - process message: [{$message->getFrom()}] {$message->getSubject()}");
                    $report = static::getReportFromMessage($message);
                    $email = static::emailFactoryByMessage($message);
                    $institution = static::getInstitutionByMessage($message);
                    $user = static::getUserByMessage($message);

                    $activityData = [
                        'comment' => Html::replaceMultipleLineBreaks($email->body),
                        'original_value' => $email->body,
                    ];

                    $type = null;

                    if ($report !== null) {
                        // if report found, then process it
                        $log->addOutput(' -- report found: ' . $report->getUniqueName());
                        $email->report_id = $report->id;

                        if (!$email->save()) {
                            $log->addErrorMessage(VarDumper::dumpAsString($email->getErrors()));
                            continue;
                        }

                        $activityData['email_id'] = $email->id;

                        switch ($report->status) {
                            case Report::STATUS_WAITING_FOR_ANSWER:
                                $type = ReportActivity::TYPE_ANSWER;
                                $activityData['institution_id'] = $institution === null ? null : $institution->id;
                                $log->addOutput(' -- create ANSWER activity');
                                break;
                            case Report::STATUS_WAITING_FOR_RESPONSE:
                                $type = ReportActivity::TYPE_RESPONSE;
                                $activityData['user_id'] = $user === null ? null : $user->id;
                                $log->addOutput(' -- create RESPONSE activity');
                                break;
                            case Report::STATUS_WAITING_FOR_SOLUTION:
                                $type = ReportActivity::TYPE_NEW_INFO;
                                $activityData['user_id'] = $user === null ? null : $user->id;
                                $log->addOutput(' -- create NEW_INFO activity');
                                break;
                            default:
                                $log->addOutput(' -- skip to create activity');
                                continue 2;
                                break;
                        }

                        $activity = $report->constructActivity($type, $activityData);
                        $folder = $report->getFileUrl();
                    } else {
                        $log->addOutput(' -- No report found. Create incoming email activity');

                        // if no report found, then create an incoming_email activity for admins
                        if (!$email->save()) {
                            $log->addErrorMessage(VarDumper::dumpAsString($email->getErrors()));
                            continue;
                        }

                        $activity = new ReportActivity([
                            'type' => ReportActivity::TYPE_INCOMING_EMAIL,
                            'institution_id' => $institution === null ? null : $institution->id,
                            'user_id' => $user === null ? null : $user->id,
                            'email_id' => $email->id,
                        ]);
                        $folder = ReportAttachment::FOLDER_EMAIL_TEMP;
                    }

                    if ($activity === null) {
                        $log->addErrorMessage('-- No ReportActivity created');
                        continue;
                    }

                    if (!$activity->save()) {
                        $log->addErrorMessage(' -- ' . VarDumper::dumpAsString($activity->getErrors()));
                        continue;
                    }

                    // assign the attachments for the activity
                    foreach ($message->getAttachments() as $attachment) {
                        $log->addOutput(' -- save attachment: ' . $attachment->getFilename());
                        $dbAttachment = new ReportAttachment([
                            'name' => $attachment->getFilename(),
                            'report_activity_id' => $activity->id,
                            'email_id' => $email->id,
                            'type' => ReportAttachment::TYPE_ATTACHMENT,
                            'report_id' => ArrayHelper::getValue($report, 'id'),
                            'storage' => StorageInterface::S3,
                        ]);

                        if (!$dbAttachment->save()) {
                            $log->addErrorMessage(' -- ' . VarDumper::dumpAsString($dbAttachment->getErrors()));
                            continue;
                        }

                        $s3 = new S3();
                        $s3->upload(
                            $dbAttachment->getUploadPath($folder),
                            $attachment->getContent()
                        );
                    }

                    //statically giving report activity type to give the same response
                    EmailHelper::sendMail($activity, ReportActivity::TYPE_ANSWER);
                }

                $log->save();
            }
        } catch (\Exception $e) {
            $log->addErrorMessage('An error happened during downloading an email ' . $e->getMessage());
        }

        $log->save();
    }

    /**
     * @param $message GmailEmailWrapper
     * @return Institution
     */
    public static function getInstitutionByMessage($message)
    {
        /* @var \app\models\db\Institution $institution */
        $institution = Institution::find()
            ->leftJoin(Contact::tableName(), '`contact`.`institution_id` = `institution`.`id`')
            ->where(['contact.email' => $message->getFromAddress()])
            ->one();

        return $institution;
    }

    /**
     * @param $message GmailEmailWrapper
     * @return Email
     */
    public static function emailFactoryByMessage($message)
    {
        $to = $message->getToAddress();
        $email = new Email([
            'from' => $message->getFromAddress(),
            'to' => $to,
            'subject' => $message->getSubject() ?: '',
            'body' => $message->getBodyText() ?: '',
            'direction' => 0,
        ]);

        return $email;
    }

    /**
     * @param $message GmailEmailWrapper
     * @return null|User
     */
    public static function getUserByMessage($message)
    {
        return User::findOne(['email' => $message->getFromAddress()]);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCitiesWithEmailAccount()
    {
        $cities = City::find()
            ->andWhere(['status' => City::STATUS_ACTIVE])
            ->join('join', 'gmail_email_token', 'email_address=gmail_email_token.email')
            ->asArray()
            ->all();

        return $cities;
    }

    /**
     * @return string
     */
    public static function getReportIdentifierRegexp()
    {
        return '/' . preg_quote(Yii::$app->params['report-unique-name']) . '-[^-]+-([0-9]{8})/';
    }

    /**
     * @param $city
     * @return GmailEmailWrapper[]
     */
    public static function getMessagesFromServer($city)
    {
        $token = GmailEmailToken::findOne(['email' => $city['email_address']]);

        if (empty($token)) {
            return [];
        }

        $api = new GmailApi();
        $token->applyTokenToClient($api);
        $messagesTmp = $api->listUnreadedMessages(self::BATCH_READ_EMAIL_COUNT, true);
        $messages = [];

        if (!empty($messagesTmp)) {
            foreach ($messagesTmp as $message) {
                if (!in_array($message->getFromAddress(), static::$skipFrom)) {
                    $messages[] = $message;
                }
            }
        }

        return $messages;
    }

    /**
     * @param $message GmailEmailWrapper
     * @return null|Report
     */
    public static function getReportFromMessage($message)
    {
        $report = null;
        $regex = static::getReportIdentifierRegexp();

        if (
            preg_match($regex, $message->getSubject(), $matches)
            || preg_match($regex, $message->getBodyText(), $matches)
        ) {
            // search report-unique-name in letters, then find the report
            $report = Report::findOne(['id' => (int)$matches[1]]);
        }

        return $report;
    }

    /**
     *
     */
    public static function mapEmailsWithNewContacts()
    {
        $reportActivities = ReportActivity::find()
            ->where([
                'type' => ReportActivity::TYPE_INCOMING_EMAIL,
                'institution_id' => null,
                'user_id' => null,
                'admin_id' => null,
            ])
            ->andWhere(['NOT', ['email_id' => null]])
            ->all();

        /** @var ReportActivity $reportActivity */
        foreach ($reportActivities as $reportActivity) {
            $institutionId = Contact::find()->select('institution_id')->where(['email' => $reportActivity->email->from])->asArray()->scalar();

            if ($institutionId !== false) {
                $reportActivity->institution_id = $institutionId;
                $reportActivity->save();
            }
        }
    }
}
