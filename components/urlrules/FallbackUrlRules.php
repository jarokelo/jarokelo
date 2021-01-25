<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class FallbackUrlRules extends GroupUrlRule
{
    public $rules = [
        '<controller:\w+>/<action:\w+>/<id:\d+>'     => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>'              => '<controller>/<action>',
        '<controller:\w+>/<id:\d+>'                  => '<controller>',

        '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
    ];
}
