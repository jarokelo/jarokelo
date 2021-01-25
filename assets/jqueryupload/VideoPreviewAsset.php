<?php

namespace app\assets\jqueryupload;

class VideoPreviewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload';
    public $js = [
        'js/jquery.fileupload-video.js',
    ];
    public $depends = [
        PreviewAsset::class,
    ];
}
