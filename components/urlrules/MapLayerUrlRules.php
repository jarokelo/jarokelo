<?php

namespace app\components\urlrules;

use yii\web\GroupUrlRule;

/**
 *
 */
class MapLayerUrlRules extends GroupUrlRule
{
    /**
     * @var array
     */
    public $rules = [
        'get-map-layer.js' => 'map-layer/get',
    ];
}
