<?php

namespace app\modules\legacyapi\components;

use app\models\db\User;

class HttpBasicAuth extends \yii\filters\auth\HttpBasicAuth
{
    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'Restricted area';

    /**
     * @var boolean whether to enable the authenticator. You may use this property to turn on and off
     * the authenticator according to specific setting (e.g. enable authenticator only for some controllers).
     */
    public $enabled = true;

    /**
     * @var callable a PHP callable that will authenticate the user with the HTTP basic auth information.
     * The callable receives a username and a password as its parameters. It should return an identity object
     * that matches the username and password. Null should be returned if there is no such identity.
     *
     * The following code is a typical implementation of this callable:
     *
     * ```php
     * function ($username, $password) {
     *     return \app\models\User::findOne([
     *         'username' => $username,
     *         'password' => $password,
     *     ]);
     * }
     * ```
     *
     * If this property is not set, the username information will be considered as an access token
     * while the password information will be ignored. The [[\yii\web\User::loginByAccessToken()]]
     * method will be called to authenticate and login the user.
     */
    public function init()
    {
        parent::init();
        $this->auth = function ($username, $password) {
            $user = User::findOne([
                'email' => $username,
                'status' => User::STATUS_ACTIVE,
            ]);

            if ($user !== null && $user->validatePassword($password)) {
                return $user;
            }

            return null;
        };
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        if (!$this->enabled) {
            return true;
        }

        $username = $request->getAuthUser();
        $password = $request->getAuthPassword();

        if ($this->auth) {
            if ($username !== null || $password !== null) {
                $identity = call_user_func($this->auth, $username, $password);
                if ($identity !== null) {
                    $user->switchIdentity($identity);
                } else {
                    $this->handleFailure($response);
                }
                return $identity;
            }
        } elseif ($username !== null) {
            $identity = $user->loginByAccessToken($username, get_class($this));
            if ($identity === null) {
                $this->handleFailure($response);
            }
            return $identity;
        }

        return null;
    }

    public function handleFailure($response)
    {
        ApiController::setResponseData(ApiController::ERROR_LOGIN_REQUIRED);
        $response->send();
    }
}
