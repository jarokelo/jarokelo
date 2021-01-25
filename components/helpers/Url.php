<?php

namespace app\components\helpers;

class Url
{
    /**
     * @var string the regular expression used to validate the attribute value.
     * The pattern may contain a `{schemes}` token that will be replaced
     * by a regular expression which represents the [[validSchemes]].
     */
    public static $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';
    /**
     * @var array list of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     */
    public static $validSchemes = ['http', 'https'];
    /**
     * @var string the default URI scheme. If the input doesn't contain the scheme part, the default
     * scheme will be prepended to it (thus changing the input). Defaults to null, meaning a URL must
     * contain the scheme part.
     */
    public static $defaultScheme;

    public static function isValidUrl($value)
    {
        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if (static::$defaultScheme !== null && strpos($value, '://') === false) {
                $value = static::$defaultScheme . '://' . $value;
            }

            if (strpos(static::$pattern, '{schemes}') !== false) {
                $pattern = str_replace('{schemes}', '(' . implode('|', static::$validSchemes) . ')', static::$pattern);
            } else {
                $pattern = static::$pattern;
            }

            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}
