<?php

namespace app\tests\mocha\assets;

class MochaSinonAsset extends \yii\web\AssetBundle
{
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public $sourcePath = '@app/tests/mocha/assets/vendor';
    public $js = [ 'mocha-sinon.js' ];
    public $depends = [
        'app\tests\mocha\assets\MochaAsset',
        'app\tests\mocha\assets\SinonAsset',
    ];
}
