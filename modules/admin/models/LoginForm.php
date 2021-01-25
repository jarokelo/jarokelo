<?php

namespace app\modules\admin\models;

use app\models\db\Admin;

use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * Login form for the Admins.
 *
 * @package app\modules\admin\models
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool
     */
    public $rememberMe = true;

    /**
     * @var \app\models\db\Admin|bool
     */
    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            [['rememberMe'], 'boolean'],
            [['password'], 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @internal param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            /** @var Admin $user */
            $user = $this->getUser();

            if ($user === null) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
                return;
            }

            $user->hashPasswordIfSha1($this->{$attribute});

            if (!$user->isPasswordHashed()) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
                return;
            }

            if (!$user || !$user->validatePassword($this->{$attribute})) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate() && Yii::$app->user->login($this->getUser(), $this->rememberMe ? Yii::$app->params['adminAuthKeyExpiration'] : 0)) {
            try {
                $this->getUser()->generateAuthKey();
                $this->getUser()->last_login_at = time();
                $this->getUser()->last_login_ip = Yii::$app->request->getUserIP();
                $this->getUser()->save();
            } catch (Exception $e) {
                Yii::error('Exception while updating login data! Exception: ' . print_r($e, true));
            }

            return true;
        }

        return false;
    }

    /**
     * Finds user by email.
     *
     * @return \app\models\db\Admin|null|false
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Admin::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => Yii::t('label', 'generic.email'),
            'password'   => Yii::t('label', 'generic.password'),
            'rememberMe' => Yii::t('app', 'login.form.rememberMe'),
        ];
    }
}
