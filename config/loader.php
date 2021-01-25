<?php

if (!function_exists('load_config')) {
    function load_config($config_name)
    {
        if (!defined('YII_CONFIG_ENVIRONMENT')) {
            // define YII_CONFIG_ENVIRONMENT, YII_DEBUG and YII_TRACE_LEVEL
            $env = __DIR__ . '/env.php';
            require_once($env);
        }

        $env_config_path = __DIR__ . '/' . YII_CONFIG_ENVIRONMENT . '/' . $config_name;
        if (is_file($env_config_path) && is_readable($env_config_path)) {
            return include($env_config_path);
        }

        // no env-specific config found, return empty array

        return [];
    }
}
