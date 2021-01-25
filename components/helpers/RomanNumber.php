<?php

namespace app\components\helpers;

/**
 * Check if a string is a roman number
 * @see https://stackoverflow.com/questions/6265596/how-to-convert-a-roman-numeral-to-integer-in-php
 */
class RomanNumber
{
    /**
     * Array of roman values
     * @var array
     */
    public static $romanValues = [
        'I' => 1,
        'V' => 5,
        'X' => 10,
        'L' => 50,
        'C' => 100,
        'D' => 500,
        'M' => 1000,
    ];

    /**
     * Values that should evaluate as 0
     * @var array
     */
    public static $romanZero = [
        'N',
        'nulla',
    ];

    /**
     * Regex to check if it's a roman number in the following format (to match with districts)
     *
     * I. kerület
     * IV. {text}
     *
     * @var string
     */
    public static $romanRegex = '/^(?=[MDCLXVI])M*(C[MD]|D?C{0,3})(X[CL]|L?X{0,3})(I[XV]|V?I{0,3})?\./m';

    /**
     * @param string $input
     * @return bool
     */
    public static function isRomanNumber($input)
    {
        preg_match(static::$romanRegex, $input, $matches);
        $match = false;

        // checking if isset and has value
        if (!empty($matches[3])) {
            $match = $matches[3];
        }

        if (!empty($matches[2])) {
            $match = $matches[2];
        }

        if (!empty($matches[2]) && !empty($matches[3])) {
            $match = $matches[2] . $matches[3];
        }

        return $match;
    }

    /**
     * Conversion: Roman Numeral to Integer
     * @param string $input
     * @return bool|int
     */
    public static function romanToInt($input)
    {
        //checking for zero values
        if (in_array($input, static::$romanZero)) {
            return 0;
        }

        //validating string
        if (!$input = static::isRomanNumber($input)) {
            return false;
        }

        $values = static::$romanValues;
        $result = 0;

        //iterating through characters LTR
        for ($i = 0, $length = strlen($input); $i < $length; $i++) {
            //getting value of current char
            $value = $values[$input[$i]];
            //getting value of next char - null if there is no next char
            $nextValue = !isset($input[$i + 1]) ? null : $values[$input[$i + 1]];
            //adding/subtracting value from result based on $nextValue
            $result += ($nextValue !== null && $nextValue > $value) ? - $value : $value;
        }

        return $result;
    }
}
