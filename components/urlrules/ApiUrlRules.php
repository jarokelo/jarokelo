<?php

namespace app\components\urlrules;

use yii\web\GroupUrlRule;
use yii\rest\UrlRule;

class ApiUrlRules extends GroupUrlRule
{
    public $prefix = 'api/v2';

    public $routePrefix = false;

    public $rules = [
        ''                          => 'static/api',
        '<controller:\w+>'          => 'api/<controller>/index',
        '<controller:\w+>/<id:\d+>' => 'api/<controller>/view',
    ];
}
