<?php

namespace app\components\helpers;

/**
 * Standalone helper to encrypt/decrypt data with a given salt and method
 */
final class OpenSsl
{
    /**
     * @var string
     */
    const SECURITY_SALT = '555MildlyTerrorSecuritySalt444';

    /**
     * @var string
     */
    const SECURITY_METHOD = 'AES-128-ECB';

    /**
     * @param $data
     * @return string
     * @static
     */
    public static function encrypt($data)
    {
        return openssl_encrypt($data, self::SECURITY_METHOD, self::SECURITY_SALT);
    }

    /**
     * @param $data
     * @return string
     * @static
     */
    public static function decrypt($data)
    {
        return openssl_decrypt($data, self::SECURITY_METHOD, self::SECURITY_SALT);
    }
}
