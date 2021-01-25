<?php

namespace app\components;

use app\models\db\District;
use Yii;
use app\components\helpers\OpenSsl;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\User;
use app\models\forms\PasswordRecoveryForm;
use app\models\forms\ContactForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Helper class for sending the e-mails required.
 *
 * @package app\components
 */
class EmailHelper
{
    // list of ReportActivity types that will trigger mails only to the reporter and won't trigger anything to the followers
    protected static $_ownerSpecificReportActivityTypeTriggers = [
        ReportActivity::TYPE_OPEN,
        'waiting_for_info',
        'waiting_for_response',
    ];

    public static function trigger(ReportActivity $reportActivity)
    {
        switch ($reportActivity->type) {
            case ReportActivity::TYPE_ANSWER:
                // in case of answer modification skipping email sending
                if ($reportActivity->updated_at == $reportActivity->created_at) {
                    static::sendUserReportStatusChange($reportActivity);
                    break;
                }

                break;
            case ReportActivity::TYPE_RESPONSE:
            case ReportActivity::TYPE_GET_USER_RESPONSE:
            case ReportActivity::TYPE_MOD_STATUS:
            case ReportActivity::TYPE_SEND_TO_AUTHORITY:
            case ReportActivity::TYPE_RESOLVE:
            case ReportActivity::TYPE_DELETE:
            case ReportActivity::TYPE_CLOSE:
            case ReportActivity::TYPE_INCOMING_EMAIL:
                static::sendUserReportStatusChange($reportActivity);
                break;
            case ReportActivity::TYPE_NO_ANSWER:
                static::sendInstitutionReSendReport($reportActivity);
                break;
            case ReportActivity::TYPE_COMMENT:
                static::sendCommentNotification($reportActivity);
                break;
        }
    }

    /**
     * Sends a contact form message to the address(es) in the config file.
     *
     * @param \app\models\forms\ContactForm $model
     *
     * @return bool|void
     */
    public static function sendUserContact(ContactForm $model)
    {
        if ($model === null) {
            return;
        }

        return Yii::$app->mailer->compose('user/contact', [
            'name' => $model->name,
            'email' => $model->email,
            'message' => $model->message,
        ])
            ->setFrom([$model->email => $model->name])
            ->setTo(Yii::$app->params['emailSenders']['contact'])
            ->setSubject(Yii::t('email', 'contact.subject'))
            ->send();
    }

    /**
     * Sends the Report to the Institution.
     *
     * @param \app\models\db\Report $report The Report instance
     * @param \app\models\db\Institution $institution The Institution, which receives the Report
     * @param string[] $contacts The contacts, whom will receive the Report
     * @param bool $resend True if it's not the first report notification (used by sendInstitutionReSendReport() to change the mail subject)
     */
    public static function sendInstitutionSendReport($report, $institution, $contacts, $resend = false)
    {
        if ($report === null || $institution === null || empty($contacts)) {
            return;
        }

        /** @var JarokeloMailer $mailer */
        $mailer = Yii::$app->mailer;
        $messages = [];
        $params = $mailer->getView()->params;
        $mailer->getView()->params[] = [
            'showFooter' => false,
            'showHeader' => false,
        ];

        foreach ($contacts as $contact) {
            if (!is_array($contact) || !isset($contact['email'], $contact['name'])) {
                continue;
            }

            if ($report->city->email_address) {
                $messages[] = $mailer->compose('institution/send_report', [
                    'report' => $report,
                    'institution' => $institution,
                    'name' => $contact['name'],
                ])
                    ->setFrom([$report->city->email_address => Yii::$app->name])
                    ->setTo([$contact['email'] => $contact['name']])
                    ->setSubject(($resend ? Yii::t('email', 'subject.resend_report') : Yii::t('email', 'subject.send_report')) . ' - ' . $report->getUniqueName());
            }
        }

        // Cleaning up in params
        $mailer->getView()->params = $params;

        if (!empty($messages)) {
            $mailer->sendMultiple($messages);
        }
    }

    public static function sendInstitutionReSendReport(ReportActivity $reportActivity)
    {
        $report = $reportActivity->report;
        $contacts = [];
        $reportContacts = ArrayHelper::getValue($report, 'institution.contacts', []);
        foreach ($reportContacts as $contact) {
            $contacts[] = [
                'email' => $contact->email,
                'name' => $contact->name,
            ];
        }
        static::sendInstitutionSendReport($report, $report->institution, $contacts);
        $report->sent_email_count += 1;
        $report->save(false);
    }

    public static function sendUserPasswordRecovery(PasswordRecoveryForm $model)
    {
        if ($model === null || $model->getUser() === null) {
            return;
        }

        $user = $model->getUser();

        return Yii::$app->mailer->compose('user/password-recovery', [
            'user' => $user,
            'name' => $user->first_name,
            'form' => $model,
        ])
            ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
            ->setTo($model->email)
            ->setSubject(Yii::t('email', 'password-recovery.subject'))
            ->send();
    }

    /**
     * Is this still need after registration confirmation is required?
     *
     * @param User $user
     * @deprecated since 2018-12-16
     * @static
     */
    public static function sendUserRegisteredViaNewReport(User $user)
    {
        Yii::$app->mailer->compose('user/welcome_postsubmit', [
            'name' => $user->first_name,
            'email' => $user->email,
        ])
            ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
            ->setTo([$user->email => $user->getFullName()])
            ->setSubject(Yii::t('email', 'subject.welcome_after_submit'))
            ->send();
    }

    /**
     * @param User $user
     * @static
     */
    public static function sendUserRegisteredViaRegForm(User $user)
    {
        Yii::$app->mailer->compose('user/welcome_postreg', [
            'name' => $user->first_name,
        ])
            ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
            ->setTo([$user->email => $user->getFullName()])
            ->setSubject(Yii::t('email', 'subject.welcome_after_regform'))
            ->send();
    }

    /**
     * @param User $user
     * @static
     */
    public static function sendUserRegistrationConfirm(User $user)
    {
        Yii::$app->mailer->compose('user/confirm_registration', [
            'name' => $user->first_name,
            'date' => date('Y-m-d'),
            'link' => sprintf(
                '%s/%s/%s?token=%s',
                Url::base(true), // base url
                'auth', // controller
                'confirm-registration',
                OpenSsl::encrypt(
                    sprintf(
                        '%s-%s',
                        strtotime('+1 day', time()),
                        $user->getId()
                    )
                )
            ), // token has 1 day expiration time,
        ])
            ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
            ->setTo([$user->email => $user->getFullName()])
            ->setSubject(Yii::t('auth', 'confirm-registration'))
            ->send();
    }

    public static function sendUserReportStatusChange(ReportActivity $reportActivity)
    {
        if (!$reportActivity->report) {
            return;
        }

        $type = $reportActivity->type;
        if (
            ($reportActivity->is_active_task || $reportActivity->is_active_task === null)
            && in_array($reportActivity->type, [
                ReportActivity::TYPE_ANSWER,
                ReportActivity::TYPE_RESPONSE,
                ReportActivity::TYPE_NEW_INFO,
            ])
        ) {
            return;
        }

        if ($type == ReportActivity::TYPE_MOD_STATUS) {
            $report_status = $reportActivity->new_value;
            switch ($report_status) {
                case Report::STATUS_WAITING_FOR_INFO:
                    $type = 'waiting_for_info';
                    break;
                case Report::STATUS_WAITING_FOR_SOLUTION:
                    $type = 'waiting_for_solution';
                    break;
                case Report::STATUS_WAITING_FOR_RESPONSE:
                    $type = 'waiting_for_response';
                    break;
                default:
                    return;
            }
        }

        static::sendMail($reportActivity, $type);
    }

    /**
     * @param ReportActivity $reportActivity
     * @param $type
     */
    public static function sendMail(ReportActivity $reportActivity, $type)
    {
        if (!$reportActivity->report) {
            return;
        }

        $content = null;
        $mailSubject = Yii::t('email', 'subject.' . $type) . ' - ' . $reportActivity->report->getUniqueName();

        // information text for the report owner
        $statusInfo = Yii::t('email', 'content.status.info.' . $type, [
            'reportLabel' => Yii::t('email', 'content.label.report.own'),
            'institutionName' => empty($reportActivity->report->institution_id) ? null : $reportActivity->report->institution->name,
            'reason' => Yii::t('report', 'close.reason.' . $reportActivity->new_value),
            'content' => $content,
        ]);

        // information text for the report watchers
        $statusInfoWatched = Yii::t('email', 'content.status.info.' . $type, [
            'reportLabel' => Yii::t('email', 'content.label.report.watched'),
            'institutionName' => empty($reportActivity->report->institution_id) ? null : $reportActivity->report->institution->name,
            'reason' => Yii::t('report', 'close.reason.' . $reportActivity->new_value),
        ]);

        // add followers only if $type isn't in the report-owner-only list and notification settings are set so
        $users = ((in_array($type, static::$_ownerSpecificReportActivityTypeTriggers)) ? [] : $reportActivity->report->getFollowers()->where(['not', ['id' => $reportActivity->report->user_id]])->andWhere(['notification' => User::NOTIFICATION_IMMEDIATE, 'notification_followed' => 1])->all());

        // add owner if notification settings are set so
        if ($reportActivity->report->user->notification == User::NOTIFICATION_IMMEDIATE && $reportActivity->report->user->notification_owned == 1) {
            array_unshift($users, $reportActivity->report->user);
        }

        $messages = [];

        foreach ($users as $user) {
            $messages[] = Yii::$app->mailer->compose('user/activity', [
                'name' => $user->first_name,
                'report' => $reportActivity->report,
                'infoTxt' => (($reportActivity->report->user_id == $user->id) ? $statusInfo : $statusInfoWatched),
                'voteForUs' => '', // Unused, can be cleaned up later
            ])
                ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
                ->setTo($user->email)
                ->setSubject($mailSubject);
        }

        if (!empty($messages)) {
            Yii::$app->mailer->sendMultiple($messages);
        }
    }

    /**
     * @param ReportActivity $reportActivity
     */
    public static function sendCommentNotification(ReportActivity $reportActivity)
    {
        $report = $reportActivity->report;
        if ($report->anonymous && $reportActivity->user->id === $report->user_id) {
            $params = ['fullName' => Yii::t('data', 'report.anonymous')];
        } else {
            $params = ['fullName' => $reportActivity->user->getFullName()];
        }

        $mailSubjectOwned = Yii::t('email', 'subject.new-report-comment-owned', $params);
        $mailSubjectFollowed = Yii::t('email', 'subject.new-report-comment-followed', $params);

        $infoTxtOwned = Yii::t('email', 'content.new-report-comment-owned', $params);
        $infoTxtFollowed = Yii::t('email', 'content.new-report-comment-followed', $params);

        $users = $reportActivity->getAllFollowersAndReportOwnerWithoutActivityOwner();

        foreach ($users as $user) {
            $isOwnedReport = $user->id == $reportActivity->report->user_id;
            Yii::$app->mailer->compose('user/activity', [
                'name' => $user->first_name,
                'report' => $reportActivity->report,
                'infoTxt' => $isOwnedReport ? $infoTxtOwned : $infoTxtFollowed,
            ])
                ->setFrom([Yii::$app->params['emailSenders']['no-reply'] => Yii::$app->name])
                ->setTo($user->email)
                ->setSubject($isOwnedReport ? $mailSubjectOwned : $mailSubjectFollowed)
                ->send();
        }
    }
}
