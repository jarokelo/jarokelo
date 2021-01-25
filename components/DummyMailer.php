<?php

namespace app\components;

use yii\swiftmailer\Mailer;

class DummyMailer extends Mailer
{
    public function send($message)
    {
        return true;
    }
}
