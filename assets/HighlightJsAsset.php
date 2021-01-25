<?php

namespace app\assets;

class HighlightJsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/components/highlightjs';
    public $css = [
        'styles/default.css',
    ];
    public $js = [
        'highlight.pack.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

    public $extraParams;
}
