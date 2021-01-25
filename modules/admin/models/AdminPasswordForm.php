<?php

namespace app\modules\admin\models;

use Yii;
use app\models\db\Admin;

/**
 * Admin form for creating and updating an Admin.
 *
 * @package app\modules\admin\models
 */
class AdminPasswordForm extends Admin
{
    const PASSWORD_REGEX_LENGTH = '^(.){6,}$';
    const PASSWORD_REGEX_NUMBER = '\d{1,}';
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
        return [
            'old_password' => Yii::t('data', 'admin.old_password'),
            'new_password' => Yii::t('data', 'admin.new_password'),
            'repeat_password' => Yii::t('data', 'admin.repeat_password'),
        ];
    }

    public function findPassword($attribute)
    {
        /** @var \app\models\db\Admin $user */
        $user = Admin::findOne(Yii::$app->user->id);

        if (!Yii::$app->getSecurity()->validatePassword($this->old_password, $user->password_hash)) {
            $this->addError($attribute, Yii::t('app', 'password.old_password_incorrect'));
        }
    }

    /**
     * Hashes the plaintext password.
     *
     * @param string $attribute The password attribute's name
     */
    public function hashPasswordAttribute($attribute)
    {
        if (!empty($this->$attribute)) {
            $this->hashPassword($this->$attribute);
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->is_old_password = 0;

            return true;
        }

        return false;
    }
}
