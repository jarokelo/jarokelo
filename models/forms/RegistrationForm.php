<?php

namespace app\models\forms;

use app\components\traits\PrivacyPolicyValidatorTrait;
use Yii;
use app\components\EmailHelper;
use app\components\helpers\Link;
use app\models\db\User;
use yii\helpers\ArrayHelper;

/**
 * User form for creating a new User.
 *
 * @package app\models\forms
 */
class RegistrationForm extends User
{
    use PrivacyPolicyValidatorTrait;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $password_repeat;

    public function init()
    {
        parent::init();
        $this->setAttributes([
            'notification' => self::NOTIFICATION_IMMEDIATE,
            'notification_owned' => 1,
            'notification_followed' => 1,
            'api_rate_limit' => 1,
            'status' => User::STATUS_UNCONFIRMED,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['email'], 'email'],
            [['email'], 'unique', 'message' => Yii::t('user', 'email-not-unique', [
                'urlLogin' => Link::to(Link::AUTH_LOGIN),
                'urlRecover' => Link::to(Link::AUTH_PASSWORD_RECOVERY),
            ])],

            [['password', 'password_repeat'], 'required', 'on' => ['create', 'profileManage']],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'on' => ['create', 'profileManage']],
            [['password'], 'hashPasswordAttribute', 'on' => ['create', 'profileManage']],

            [['password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_LENGTH . '/'],
            [['password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_NUMBER . '/'],
            [['password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_CAPITAL . '/'],

            [['privacy_policy'], 'validatePrivacyPolicy', 'on' => ['create']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'password' => Yii::t('user', 'form.password'),
            'password_repeat' => Yii::t('user', 'form.password_repeat'),
        ]);
    }

    /**
     * Hashes the plaintext password.
     *
     * @param string $attribute The password's attribute name
     * @internal array $params The validation's extra params
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function hashPasswordAttribute($attribute)
    {
        if (!empty($this->$attribute)) {
            $this->hashPassword($this->$attribute);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            EmailHelper::sendUserRegistrationConfirm($this);
        }
    }
}
