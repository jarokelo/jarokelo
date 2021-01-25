<?php

if (!function_exists('d')) {
    /**
     * Alias of yii\helpers\VarDumper::dump()
     *
     * @param $var
     * @param int $depth
     * @param bool $highlight
     */
    function d($var, $depth = 10, $highlight = true)
    {
        yii\helpers\VarDumper::dump($var, $depth, $highlight);
    }
}

if (!function_exists('dd')) {
    /**
     * Alias of yii\helpers\VarDumper::dump()
     * [!!!] IMPORTANT: execution will halt after call to this function
     *
     * @param $var
     * @param int $depth
     * @param bool $highlight
     */
    function dd($var, $depth = 10, $highlight = true)
    {
        yii\helpers\VarDumper::dump($var, $depth, $highlight);
        die;
    }
}
