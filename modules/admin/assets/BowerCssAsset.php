<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class BowerCssAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $css = [
        'darkroom/build/darkroom.css',
        'dropzone/dist/dropzone.css',
        'lightgallery/dist/css/lightgallery.min.css',
    ];
}
