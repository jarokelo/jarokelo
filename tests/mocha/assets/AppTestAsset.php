<?php

namespace app\tests\mocha\assets;

class AppTestAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/tests/mocha/assets/tests';
    public $js = [
        'site.js',
    ];
    public $depends = [
        'app\tests\mocha\assets\AppAsset',
    ];
}
