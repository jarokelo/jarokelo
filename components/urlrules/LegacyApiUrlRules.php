<?php

namespace app\components\urlrules;

use yii\web\GroupUrlRule;
use yii\rest\UrlRule;

class LegacyApiUrlRules extends GroupUrlRule
{
    public $prefix = 'api';

    public $routePrefix = false;

    public $rules = [
        ''          => 'static/api',
        'mesta'     => 'legacyapi/cities/index',
        'mesto'     => 'legacyapi/cities/view',
        'kategorie' => 'legacyapi/categories/index',
        'ulice'     => 'legacyapi/streets/index',
        'list'      => 'legacyapi/reports/index',
        'podnet'    => 'legacyapi/reports/view',
        'mapdata'   => 'legacyapi/reports/mapdata',
        'login'     => 'legacyapi/login/index',
        'submit'    => 'legacyapi/submit/index',
        'comment'   => 'legacyapi/comment/index',
#        '<controller:\w+>/<id:\d+>'             => 'legacyapi/<controller>/view',
    ];
}
