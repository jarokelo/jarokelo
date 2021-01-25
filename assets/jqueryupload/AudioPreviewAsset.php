<?php

namespace app\assets\jqueryupload;

class AudioPreviewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload';
    public $js = [
        'js/jquery.fileupload-audio.js',
    ];
    public $depends = [
        PreviewAsset::class,
    ];
}
