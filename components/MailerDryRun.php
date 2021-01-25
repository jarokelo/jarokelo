<?php

namespace app\components;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;
use yii\mail\MailEvent;

/**
 * MailerDryRun intercepts messages, unsets their to, cc and bcc fields,
 * and sends them to the configured email address(es).
 * The original recipients will be attached in a text file.
 */
class MailerDryRun extends Behavior
{
    /**
     * @var false|null|string|array the email address(es) to send emails to
     * this should be in the format expected by \yii\mail\MessageInterface::setTo
     * If false, this behavior is disabled.
     * If null, the behavior will throw an exception
     */
    public $email = null;

    public function beforeSend(MailEvent $event)
    {
        if ($this->email === false) {
            return;
        }
        if ($this->email === null) {
            throw new InvalidConfigException('MailerDryRun::email must be set');
        }

        $origTo = print_r($event->message->getTo(), true);
        $origCc = print_r($event->message->getCc(), true);
        $origBcc = print_r($event->message->getBcc(), true);

        $origInfo = <<<EOT
original recipients:
    to: $origTo
    cc: $origCc
    bcc: $origBcc
EOT;

        $event->message->attachContent($origInfo, [
            'fileName' => 'original_recipients.txt',
            'contentType' => 'text/plain',
        ]);
        $event->message->setTo($this->email);
        $event->message->setCc([]);
        $event->message->setBcc([]);
    }

    public function events()
    {
        return [
            BaseMailer::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }
}
