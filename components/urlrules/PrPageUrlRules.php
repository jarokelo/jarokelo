<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class PrPageUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = Link::PR_PAGE;

    public $routePrefix = false;

    public $rules = [
        self::PR_PAGE_SLUG => 'pr-page/view',
        self::PR_PAGE_SLUG . '/hirek/' . '<id:\d+>' => 'pr-page/show',
    ];
}
