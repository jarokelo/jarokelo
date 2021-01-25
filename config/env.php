<?php

if (!defined('YII_CONFIG_ENVIRONMENT')) {
    $env_file = __DIR__ . '/ENV';

    if (is_file($env_file) && is_readable($env_file)) {
        $env = file_get_contents($env_file);
    } else {
        $env = '';
    }

    if (preg_match('/^([a-z0-9]+)/', $env, $matches)) {
        $env = $matches[1];
    }
    if (empty($env)) {
        $env = 'development';
    }

    define('YII_CONFIG_ENVIRONMENT', $env);
    if ($env == 'development') {
        error_reporting(-1);
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        defined('YII_ENV') or define('YII_ENV', 'dev');
    }
}
