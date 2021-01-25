<?php

require(__DIR__ . '/loader.php');

$env_specific = load_config('common.php');
$env_specific_local = load_config('common.local.php');

$dbDsn = getenv('DB_DSN');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

$dbEnvConfig = [];
if ($dbDsn !== false) {
    $dbEnvConfig['dsn'] = $dbDsn;
}
if ($dbUsername !== false) {
    $dbEnvConfig['username'] = $dbUsername;
}
if ($dbPassword !== false) {
    $dbEnvConfig['password'] = $dbPassword;
}

$logVars = [
    '_GET',
    '_POST',
    '!_POST.password',
    '!_POST.password_repeat',
    '_FILES',
    '_COOKIE',
    '_SESSION',
    '_SERVER',
    '!_SERVER.DB_PASSWORD',
    '!_SERVER.PHP_AUTH_PW',
    '!_SERVER.HTTP_PASSWORD',
];

$common = [
    'name' => 'myProject',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'id' => 'my_project',
    'bootstrap' => [
        'log',
        function () {
            if ($language = Yii::$app->session->get('language')) {
                Yii::$app->language = $language;
            }
        },
    ],
    'sourceLanguage' => '00',
    'language' => 'hu',
    'components' => [
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'clientId' => '',
                ],
            ],
        ],
        'bundleManager' => [
            'class' => \app\assets\BundleManager::class,
            'deployBundles' => [],
        ],
        'session' => [
            'class' => 'yii\web\Session',  // for use session in console application
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],
        'image' => [
            'class' => 'yii\image\ImageDriver',
            'driver' => 'GD', //GD or Imagick
        ],
        'mailer' => [
            'class' => 'app\components\JarokeloMailer',
        ],
        'sentry' => [
            'class' => \app\components\sentry\SentryComponent::class,
            'environment' => YII_CONFIG_ENVIRONMENT, // if not set, the default is `development`
            'jsNotifier' => false, // to collect JS errors
            'clientOptions' => [ // raven-js config parameter
                'whitelistUrls' => [ // collect JS errors from these urls
                    'https://myproject.hu',
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \app\components\sentry\SentryTarget::class,
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:401',
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:405',
                    ],
                    'logVars' => $logVars,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'maxLogFiles' => 10,
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => $logVars,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'maxLogFiles' => 1,
                    'logFile' => '@runtime/logs/api.log',
                    'exportInterval' => 1,
                    'categories' => [
                        'legacy-api',
                    ],
                    'logVars' => $logVars,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/404.log',
                    'levels' => ['error', 'warning'],
                    'maxLogFiles' => 1,
                    'categories' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => $logVars,
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
                'kvcolor' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'formatter' => [
            'datetimeFormat' => 'php:Y. mm d. H:i',
            'dateFormat' => 'php:Y. mm d.',
        ],
        'preload' => [
            'class' => 'app\components\Preload',
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ],
    'aliases' => [
        '@mailImages' => '@app/web/images/mail',
    ],
    'params' => [
        'google' => [
            'api_key_http' => '',
            'api_key_server' => '',
        ],
        'gtm' => [
            'key' => '',
        ],
        'mobile' => [
            'enabled' => false,
            'links' => [
                'ios' => 'https://itunes.apple.com/us/app/myproject/id',
                'android' => 'https://play.google.com/store/apps/details?id=com.softec.android.myproject',
            ],
        ],
        'emailSenders' => [
            'info' => 'info@myproject.hu',
            'contact' => 'contact@myproject.net',
            'no-reply' => 'no-reply@myproject.hu',
            'reports' => 'report@myproject.hu',
        ],
        'aws' => [
            's3' => [
                'baseUrl' => 's3://myproject/',
                'bucket' => 'myprojectbucket-s3',
                'version' => 'version', // EG. latest
                'region' => 'region', // EG. eu-north-1
                'baseObjectUrl' => 'https://myproject-s3.amazonaws.com/',
            ],
        ],
        'map' => [
            'defaultPosition' => [ // Budapest
                'lat' => 47.497394536689065,
                'lng' => 19.054870158433914,
            ],
            'mapboxToken' => '',
        ],
        'report-unique-name' => 'MYPROJECT',
        'defaultCityId' => 1, // budapest
        'adminAuthKeyExpiration' => 24 * 60 * 60,
        'publicAuthKeyExpiration' => 30 * 24 * 60 * 60,
        'answerWaitDays' => 40,
        'answerWaitDaysAfterResend' => 30,
        'responseWaitDays' => 14,
        'newInfoWaitDays' => 14,
        'quickSearchDisplayCount' => 3,
        'default_admin_permissions' => 65550, // Report: Edit + Delete, Institution: View
        'mailServer' => [
            'host' => 'imap.gmail.com',
            'port' => 993,
        ],
        'edm' => [
            'width' => 60,
            'height' => 60,
            'fallback' => 'roadblock_60x60.png',
        ],
        'thumb' => [
            'width' => 290,
            'height' => 207,
            'fallback' => 'roadblock_290x207.png',
        ],
        'medium' => [
            'width' => 768,
            'height' => 432,
            'fallback' => 'roadblock_768x432.png',
        ],
        'original' => [
            'maxWidth' => 2048,
            'maxHeight' => 1536,
        ],
        'video' => [
            'width' => 640,
            'height' => 390,
        ],
        'webSocketServerPort' => 8606,
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '',
        ],
        'cache' => [
            'db' => [
                'report' => 600,
                'user' => 600,
                'reportCommentCount' => 600,
                'userRanks' => 60 * 60 * 24, // daily
                'commonStats' => 300,
                'citySlugById' => 600,
                'cityIdBySlug' => 600,
                'generalDbQuery' => 600,
                'intervalDaily' => 60 * 60 * 24,
            ],
        ],
        'rss' => [
            'cacheTime' => 1800,
        ],
        'comments' => [
            'cutAfterCharacterLength' => 200,
            'overlayAfterCharacterLength' => 500,
        ],
        'pr_page_news' => [
            'cutAfterCharacterLength' => [
                'short' => 150,
                'long' => 400,
            ],
        ],
        'allowed_mime_types' => [
            'image' => [
                'image/png',
                'image/x-png',
                'image/jpeg',
                'image/pjpeg',
                'image/gif',
                'image/bmp',
            ],
            'generic' => [
                'application/msword',
                'application/octet-stream',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text',
                'application/pdf',
                'text/rtf',
                'text/csv',
                'text/plain',
                'application/vnd.ms-excel',
                'application/vnd.ms-office',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip', // needed for .xlsx format
            ],
        ],
        // gmail token data
        'gmail_client' => [
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'redirect_uris' => [
                'https://myproject.hu',
                'https://myproject.hu/gmail/store-token',
            ],
        ],
        'xMailerHeader' => '',
    ],
];

$envConfig = [
    'components' => [
        'db' => $dbEnvConfig,
    ],
];

return yii\helpers\ArrayHelper::merge($common, $env_specific, $env_specific_local, $envConfig);
