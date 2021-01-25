<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class PublicProfileUrlRules extends GroupUrlRule
{
    public $prefix = false;

    public $routePrefix = false;

    public $rules = [
        Link::PROFILES . '/<id:\d+>' => 'profile/view',
    ];
}
