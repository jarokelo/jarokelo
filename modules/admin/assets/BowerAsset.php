<?php

namespace app\modules\admin\assets;

use app\assets\AssetBundle;

class BowerAsset extends AssetBundle
{
    public $devPath = '@bower';
    public $distPath = '@app/modules/admin/assets/vendor/dist';

    public $js = [
        'js/vendor.js',
    ];

    public $devJs = [
        'js/vendor.js' => [
            'fabric/dist/fabric.js',
            'dropzone/dist/dropzone.js',
            'darkroom/build/darkroom.js',
            'lightgallery/dist/js/lightgallery.min.js',
            'lightgallery/dist/js/lg-video.min.js',
            'lightgallery/dist/js/lg-zoom.min.js',
            'lightgallery/dist/js/lg-thumbnail.min.js',
            'keyboardjs/dist/keyboard.min.js',
        ],
    ];

    public $extraParams = [
        'ignoreErrors' => true,
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\admin\assets\BowerCssAsset',
    ];
}
