<?php

namespace app\components\helpers;

class Key
{
    /**
     * Generates a random a key.
     *
     * @param integer $length [optional]
     *
     * @return string $key
     */
    public static function generate($length = 6)
    {
        $pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $key = '';
        for ($i = 1; $i <= $length; $i++) {
            $key .= $pattern[mt_rand(0, strlen($pattern) - 1)];
        }

        return $key;
    }
}
