<?php

namespace app\models\forms;

use app\components\EmailHelper;
use app\components\helpers\Link;
use app\models\db\User;

use Yii;

use yii\base\Model;

class PasswordRecoveryForm extends Model
{
    /**
     * @var string
     */
    public $email;

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
            [['email'], 'required'],
            [['email'], 'string'],
            [['email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('label', 'generic.email'),
        ];
    }

    /**
     * Finds user by email.
     *
     * @return \app\models\db\User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    public function processRecovery()
    {
        $user = $this->getUser();

        if ($user !== null) {
            $user->setPasswordRecoveryToken();

            if ($user->save()) {
                return EmailHelper::sendUserPasswordRecovery($this);
            }
        }

        return false;
    }

    public function generateNewPasswordUrl()
    {
        return Link::to(Link::AUTH_SET_NEW_PASSWORD, [
            'email' => $this->email,
            'token' => $this->getUser()->password_recovery_token,
        ]);
    }
}
