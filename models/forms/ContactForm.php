<?php

namespace app\models\forms;

use app\components\EmailHelper;
use Yii;
use yii\base\Model;

class ContactForm extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'message', 'email'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['name', 'message', 'email'], 'trim'],
            [['name', 'message', 'email'], 'default'],
            [['name', 'email', 'reCaptcha'], 'required'],
            [['message'], 'required', 'message' => 'Kérjük írd be az üzeneted.'],
            [['email'], 'email'],
            [['name', 'message', 'email'], 'string'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => Yii::$app->params['reCaptcha']['secret']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('contactform', 'label.name'),
            'email' => Yii::t('contactform', 'label.email'),
            'message' => Yii::t('contactform', 'label.message'),
        ];
    }

    /**
     * Submits the contact form to the recipients.
     *
     * @return bool True, if the submit was successful
     */
    public function handleContact()
    {
        return EmailHelper::sendUserContact($this);
    }
}
