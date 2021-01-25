<?php

namespace app\components\helpers;

/**
 *
 */
class CookieConsent
{
    const NAME = 'cookieconsent_status_ESSENTIAL';

    /**
     * @return bool
     */
    public static function isAllowed()
    {
        return isset($_COOKIE[self::NAME]) && 'ALLOW' == $_COOKIE[self::NAME];
    }
}
