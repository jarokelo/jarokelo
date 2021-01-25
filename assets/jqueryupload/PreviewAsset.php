<?php

namespace app\assets\jqueryupload;

class PreviewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload';
    public $js = [
        'js/jquery.fileupload-process.js',
    ];
    public $depends = [
        LoadImageAsset::class,
        CanvasToBlobAsset::class,
        FileuploadAsset::class,
    ];
}
