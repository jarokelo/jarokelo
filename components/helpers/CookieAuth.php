<?php

namespace app\components\helpers;

use Yii;

/**
 *
 */
class CookieAuth
{
    /**
     * Storing checkbox remember me value to make possible
     * social logged in status persistent
     *
     * @var string
     */
    const REMEMBER_ME = 'rememberMe';

    /**
     * Preparing rememberMe session if it's not set yet
     * @static
     */
    public static function prepareSession()
    {
        if (!Yii::$app->session->get(self::REMEMBER_ME)) {
            Yii::$app->session->set(self::REMEMBER_ME, true);
        }
    }

    /**
     * Removing temporarily set session if it's already set
     * @static
     */
    public static function removeSession()
    {
        if (Yii::$app->session->get(self::REMEMBER_ME)) {
            Yii::$app->session->remove(self::REMEMBER_ME);
        }
    }
}
