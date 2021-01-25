<?php

namespace app\models\forms;

use Yii;
use app\models\db\User;
use yii\base\Model;

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
     * @var boolean
     */
    public $rememberMe;

    /**
     * @var \app\models\db\User
     */
    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string'],
            [['email'], 'email'],
            [['password'], 'validatePassword'],
            [['rememberMe'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'    => Yii::t('label', 'generic.email'),
            'password' => Yii::t('label', 'generic.password'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /** @var User $user */
            $user = $this->getUser([User::STATUS_ACTIVE, User::STATUS_UNCONFIRMED]);

            if ($user === null) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
                return;
            }

            $user->hashPasswordIfSha1($this->{$attribute});

            if (!$user->isPasswordHashed()) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
                return;
            }

            if ($user->status == User::STATUS_UNCONFIRMED) {
                $this->addError($attribute, Yii::t('auth', 'unconfirmed-user'));
            }

            if (!$user || !$user->validatePassword($this->{$attribute})) {
                $this->addError($attribute, Yii::t('auth', 'wrong-username-or-password'));
            }
        }
    }

    /**
     * Logs in the User.
     *
     * @return bool True, if the User can be logged in
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if (Yii::$app->user->login($user, $duration = $this->rememberMe ? Yii::$app->params['publicAuthKeyExpiration'] : 0)) {
                $user->generateAuthKey($duration);
                $user->last_login_at = time();
                $user->last_login_ip = Yii::$app->request->getUserIP();
                $user->save();
                return true;
            }
        }

        return false;
    }

    /**
     * Finds user by email.
     *
     * @param array $status by default fetching active status users
     * @return \app\models\db\User|null
     */
    public function getUser($status = [User::STATUS_ACTIVE])
    {
        if (!$this->_user) {
            $this->_user = User::findByEmail($this->email, $status);
        }

        return $this->_user;
    }
}
