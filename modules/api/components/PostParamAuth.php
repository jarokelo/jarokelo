<?php

namespace app\modules\api\components;

use yii\filters\auth\AuthMethod;

class PostParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'api_token';

    /**
     * @var boolean whether to enable the authenticator. You may use this property to turn on and off
     * the authenticator according to specific setting (e.g. enable authenticator only for some controllers).
     */
    public $enabled = true;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        if (!$this->enabled) {
            return true;
        }

        $accessToken = $request->post($this->tokenParam);

        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));

            if ($identity !== null) {
                return $identity;
            }
        }

        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
