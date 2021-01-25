<?php

namespace app\modules\admin\models;

use app\models\db\Admin;

use Yii;

/**
 * Admin form for creating and updating an Admin.
 *
 * @package app\modules\admin\models
 */
class AdminForm extends Admin
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $password_repeat;

    /**
     * @var array
     */
    public $connectedCities;

    /**
     * @var array
     */
    public $connectedPrPages;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_name', 'first_name', 'phone_number'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['last_name', 'first_name', 'phone_number'], 'trim'],
            [['last_name', 'first_name', 'phone_number'], 'default'],
            [['email', 'last_name', 'first_name'], 'required'],
            [['permissions', 'status', 'last_login_at', 'created_at', 'updated_at'], 'integer'],
            [['password_hash', 'last_name', 'first_name', 'phone_number', 'last_login_ip', 'image_file_name'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],

            [['password', 'password_repeat'], 'required', 'on' => ['create']],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'on' => ['create', 'update']],
            [['password'], 'hashPasswordAttribute', 'on' => ['create', 'update']],

            [['connectedCities', 'connectedPrPages'], 'safe', 'on' => ['update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'password'        => $this->isNewRecord ? Yii::t('data', 'admin.form.password') : Yii::t('data', 'admin.form.new_password'),
            'password_repeat' => $this->isNewRecord ? Yii::t('data', 'admin.form.password_repeat') : Yii::t('data', 'admin.form.new_password_repeat'),
        ]);
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
}
