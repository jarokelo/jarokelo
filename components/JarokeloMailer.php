<?php

namespace app\components;

use yii\helpers\Json;
use app\models\db\EmailLog;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;
use Yii;

class JarokeloMailer extends Mailer
{
    /**
     * @inheritdoc
     */
    public function beforeSend($message)
    {
        if (parent::beforeSend($message)) {
            /** @var $message Message */
            $message->getSwiftMessage()->getHeaders()->addTextHeader('X-mailer', Yii::$app->params['xMailerHeader']);

            return true;
        }

        return false;
    }

    /**
     * Catching all outgoing email to store some details in database
     * @inheritdoc
     */
    public function afterSend($message, $isSuccessful)
    {
        parent::afterSend($message, $isSuccessful);

        try {
            /** @var Message $message */
            $switfMessage = $message->getSwiftMessage();
            (new EmailLog([
                '_get' => Json::encode($_GET),
                '_post' => Json::encode($_POST),
                '_server' => Json::encode($_SERVER),
                'from' => Json::encode($switfMessage->getFrom()),
                'to' => Json::encode($switfMessage->getTo()),
                'subject' => $switfMessage->getSubject(),
                'is_successful' => $isSuccessful,
            ]))->save();
        } catch (\Exception $e) {
            // in case of exception workflow shouldn't break
        }
    }
}
