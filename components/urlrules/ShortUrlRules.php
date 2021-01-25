<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class ShortUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = false;

    public $routePrefix = false;

    public $rules = [
        '<id:\d+>' => 'report/view',
        Link::HOME => 'report/index',
    ];
}
