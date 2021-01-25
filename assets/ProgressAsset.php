<?php

namespace app\assets;

class ProgressAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/progress.min.js' => [
            'js/progress.js',
        ],
    ];
    public $js = [
        'js/progress.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $extraParams;
}
