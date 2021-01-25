<?php

namespace app\assets;

class ButtonAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/button.min.js' => [
            'js/button.js',
        ],
    ];

    public $js = [
        'js/button.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $extraParams;
}
