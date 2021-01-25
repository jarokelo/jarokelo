<?php

namespace app\modules\api\components;

use app\models\db\User;
use Yii;
use yii\filters\auth\AuthMethod;

class LoginAuth extends AuthMethod
{
    public function getUser($request)
    {
        parent::init();

        $user = User::findOne([
            'email' => $request->post('email'),
            'status' => User::STATUS_ACTIVE,
        ]);

        if ($user !== null && $user->validatePassword($request->post('password'))) {
            return $user;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $user = $this->getUser($request);

        if ($user !== null && Yii::$app->user->login($user)) {
            return $user;
        }

        $this->handleFailure($response);
    }
}
