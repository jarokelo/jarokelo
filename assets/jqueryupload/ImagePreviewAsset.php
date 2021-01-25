<?php

namespace app\assets\jqueryupload;

class ImagePreviewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload';
    public $js = [
        'js/jquery.fileupload-image.js',
    ];
    public $depends = [
        PreviewAsset::class,
    ];
}
