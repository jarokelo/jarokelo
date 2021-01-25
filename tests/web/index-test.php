<?php

include '../c3.php';

// NOTE: Make sure this file is not accessible when deployed to production
/*if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}*/

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../codeception/config/acceptance.php');

$config['controllerNamespace'] = 'app\tests\mocha\controllers';
$config['viewPath'] = dirname(__DIR__) . '/mocha/views';
$config['defaultRoute'] = 'site/index';

(new yii\web\Application($config))->run();
