<?php

namespace app\models\forms;

use app\models\db\User;

use Yii;

use yii\helpers\ArrayHelper;

/**
 * User form for creating a new User.
 *
 * @package app\models\forms
 */
class NewPasswordForm extends User
{
    /**
     * @var string
     */
    public $new_password;

    /**
     * @var string
     */
    public $repeat_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_password', 'repeat_password'], 'required'],
            [['repeat_password'], 'compare', 'compareAttribute' => 'new_password'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_LENGTH . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_NUMBER . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_CAPITAL . '/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'new_password' => Yii::t('user', 'form.new_password'),
            'repeat_password' => Yii::t('user', 'form.repeat_password'),
        ]);
    }

    public function setNewPassword()
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->new_password);
        $this->password_recovery_token = null;
    }
}
