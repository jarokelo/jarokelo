<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class WidgetUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = false;

    public $routePrefix = false;

    public $rules = [
        'widget'           => 'widget/index',
        'widget/configure' => 'widget/configure',
    ];
}
