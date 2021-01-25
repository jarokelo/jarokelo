<?php

namespace app\modules\api\components;

use app\models\db\User;

class AppUser extends User
{
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }
}
