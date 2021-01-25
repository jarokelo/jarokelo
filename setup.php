<?php

$loader = require('vendor/autoload.php');
$loader->addPsr4('app\\', __DIR__);

// @codingStandardsIgnoreStart
class setup extends \app\setup\Setup
// @codingStandardsIgnoreEnd
{
    // These should be set in setup_paths.php
    // protected static $serverWritablePaths = [];

    protected static $baseDir = __DIR__;

    protected static $defaultEnvironment = 'production';

    protected static $environments = [
        'development',
        'staging',
        'production',
    ];

    protected static $defaultServerGroup = 'apache';

    protected static $serverGroups = [
        'apache',
        'www-data',
    ];

    protected static $otherWritablePaths = [
        'runtime/mail',
        'runtime/logs',
        'runtime/cache',
        'runtime/mpdf-fontcache',
        'web/assets',
        'testweb/assets',
        'tests/codeception/_output',
        'tests/web/assets',

    ];

    public static function run()
    {
        $sharedPaths = require('setup_paths.php');
        static::$sharedWritablePaths = $sharedPaths;
        parent::run();
    }
}

setup::run(isset($_SERVER['argv']) ? $_SERVER['argv'] : []);
