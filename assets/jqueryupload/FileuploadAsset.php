<?php

namespace app\assets\jqueryupload;

class FileuploadAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/assets/jqueryupload';
    public $js = [
        'js/ajaxupload.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
