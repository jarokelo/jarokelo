<?php

if (!function_exists('d')) {
    /**
     * @param $var
     */
    function d(...$var)
    {
        yii\helpers\VarDumper::dump($var, 10, true);
    }
}

if (!function_exists('dd')) {
    /**
     * @param $var
     */
    function dd(...$var)
    {
        yii\helpers\VarDumper::dump($var, 10, true);
        die;
    }
}

if (!function_exists('alert')) {
    /**
     * @param $var
     */
    function alert(...$var)
    {
        yii\helpers\VarDumper::dump($var, 10, true);
    }
}
