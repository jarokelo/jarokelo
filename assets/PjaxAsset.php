<?php

namespace app\assets;

class PjaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/nkovacs/jquery-pjax';
    public $js = [
        'jquery.pjax.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}
