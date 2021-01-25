<?php

namespace app\assets;

class MapAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/map.min.js' => [
            'js/map.js',
        ],
    ];

    public $js = [
        'js/map.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $extraParams;
}
