<?php

use yii\web\UrlNormalizer;

require(__DIR__ . '/loader.php');

$common_cfg = require(__DIR__ . '/common.php');

$env_specific = load_config('web.php');
$env_specific_local = load_config('web.local.php');

// $cookieSuffix must be unique on the domain.
// This uses SCRIPT_NAME to make sure it is unique, in case the id is not.
$cookieSuffix = '_' . md5($common_cfg['id'] . '$' . (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_NAME'] : ''));
// You may change $cookieSuffix to this if the id in common.php is unique.
//$cookieSuffix = '_' . md5($common_cfg['id']);

$config = [
    'components' => [
        'feed' => [
            'class' => 'app\components\FeedDriver',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\components\UserComponent',
            'identityClass' => 'app\models\db\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity' . $cookieSuffix,
                'httpOnly' => true,
                'path' => '/;SameSite=None',
                'secure' => true,
            ],
            'idParam' => 'frontendId',
        ],
        'appUser' => [
            'class' => 'app\modules\api\components\AppUserComponent',
            'identityClass' => 'app\modules\api\components\AppUser',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'authClientCollection' => [
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'authUrl' => 'https://www.facebook.com/dialog/oauth',
                    'scope' => 'public_profile,email',
                    'attributeNames' => ['id', 'email', 'first_name', 'last_name', 'picture.type(large)'],
                ],
                'google' => [
                    'class' => 'yii\authclient\clients\GoogleOAuth',
                ],
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf' . $cookieSuffix,
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/;SameSite=None',
                'secure' => true,
            ],
        ],
        'session' => [
            'name' => '_session' . $cookieSuffix,
            'cookieParams' => [
                'lifetime' => 3600, // 1 hour
                'httpOnly' => true,
                'secure' => true,
                'path' => '/;SameSite=None',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'static/error',
        ],
        'assetManager' => [
            'class' => \app\assets\AssetManager::class,
            'bundles' => [
                // override default jquery asset to use cdn with fallback
                'yii\web\JqueryAsset' => [
                    'class' => 'app\assets\JqueryAsset',
                    'fallback' => 'app\assets\JqueryAssetLocal',
                    'check' => 'window.jQuery',
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\widgets\PjaxAsset' => [
                    'class' => 'app\assets\PjaxAsset',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
            ],
            'rules' => [
                // Home
                ''       => 'about/middleware',
                'widget' => 'widget/index',

                // Other routes
                ['class' => 'app\components\urlrules\ApiUrlRules'],
                ['class' => 'app\components\urlrules\LegacyApiUrlRules'],
                ['class' => 'app\components\urlrules\ShortUrlRules'],
                ['class' => 'app\components\urlrules\AboutUrlRules'],
                ['class' => 'app\components\urlrules\ReportsUrlRules'],
                ['class' => 'app\components\urlrules\ReportUrlRules'],
                ['class' => 'app\components\urlrules\WidgetUrlRules'],
                ['class' => 'app\components\urlrules\StatisticsUrlRules'],
                ['class' => 'app\components\urlrules\AuthUrlRules'],
                ['class' => 'app\components\urlrules\ProfileUrlRules'],
                ['class' => 'app\components\urlrules\PublicProfileUrlRules'],
                ['class' => 'app\components\urlrules\AdminUrlRules'],
                ['class' => 'app\components\urlrules\FallbackUrlRules'],
                ['class' => 'app\components\urlrules\PrPageUrlRules'],
                ['class' => 'app\components\urlrules\MapLayerUrlRules'],
            ],
        ],
        'response' => [
            'class' => 'app\components\Response',
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'encodeOptions' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'defaultRoute' => 'auth',
            'components' => [
                'user' => [
                    'class' => 'app\components\UserComponent',
                    'enableAutoLogin' => false,
                    'identityCookie' => ['name' => '_aidentity' . $cookieSuffix, 'httpOnly' => true],
                    'authTimeout' => 2 * 60 * 60,
                    'idParam' => 'adminId',
                ],
            ],
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
            'components' => [
                'user' => [
                    'class' => 'app\modules\api\components\AppUserComponent',
                    'identityClass' => 'app\modules\api\components\AppUser',
                    'enableAutoLogin' => false,
                    'enableSession' => false,
                ],
            ],
        ],
        'legacyapi' => [
            'class' => 'app\modules\legacyapi\Module',
            'components' => [
                'user' => [
                    'class' => 'app\components\UserComponent',
                    'identityClass' => 'app\models\db\User',
                    'enableAutoLogin' => false,
                    'enableSession' => false,
                ],
            ],
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
    ],
    'aliases' => [
        '@layouts' => '@app/views/layouts',
        '@hero' => '@layouts/_hero',
        '@footer' => '@layouts/_footer',
        '@adminViews' => '@app/modules/admin/views',
    ],
    'params' => [],
];

return yii\helpers\ArrayHelper::merge($common_cfg, $config, $env_specific, $env_specific_local);
