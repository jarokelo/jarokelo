<?php

namespace app\tests\mocha\assets;

/**
 * This asset loads only someplugin.js from AppAsset,
 * because that's the only part we can test.
 */
class AppAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/assets/main/src';
    public $js = [
        'js/someplugin.js',
    ];
}
