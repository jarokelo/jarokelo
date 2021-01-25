<?php

namespace app\modules\admin\assets;

use app\assets\AssetBundle;

class DropzoneAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/modules/admin/assets/main/dist';

    public $devJs = [
        'js/dropzone-upload.min.js' => [
            'js/upload.js',
        ],
    ];

    public $js = [
        'js/dropzone-upload.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\admin\assets\BowerAsset',
    ];

    public $extraParams;
}
