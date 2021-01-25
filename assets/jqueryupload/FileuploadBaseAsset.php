<?php

namespace app\assets\jqueryupload;

class FileuploadBaseAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload';
    public $js = [
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
}
