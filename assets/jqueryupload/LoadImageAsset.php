<?php

namespace app\assets\jqueryupload;

class LoadImageAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-load-image';
    public $js = [
        'js/load-image.all.min.js',
    ];
}
