<?php

namespace app\models\forms;

use app\components\helpers\Link;
use app\models\db\User;

use Yii;

use yii\helpers\ArrayHelper;

/**
 * User form for creating a new User.
 *
 * @package app\models\forms
 */
class PasswordChangeForm extends User
{
    const PASSWORD_REGEX_LENGTH  = '^(.){6,}$';
    const PASSWORD_REGEX_NUMBER  = '\d{1,}';
    const PASSWORD_REGEX_CAPITAL = '[A-Z]{1,}';

    /**
     * @var string
     */
    public $old_password;

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
            [['old_password', 'new_password', 'repeat_password'], 'required'],
            [['old_password'], 'findPassword'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_LENGTH . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_NUMBER . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_CAPITAL . '/'],
            [['repeat_password'], 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'old_password' => Yii::t('user', 'form.old_password'),
            'new_password' => Yii::t('user', 'form.new_password'),
            'repeat_password' => Yii::t('user', 'form.repeat_password'),
        ]);
    }

    public function findPassword($attribute)
    {
        /** @var \app\models\db\User $user */
        $user = User::findOne(Yii::$app->user->id);

        if (!Yii::$app->getSecurity()->validatePassword($this->old_password, $user->password_hash)) {
            $this->addError($attribute, Yii::t('app', 'password.old_password_incorrect'));
        }
    }
}
