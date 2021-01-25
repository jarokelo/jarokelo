<?php

namespace app\tests\mocha\assets;

class SinonAsset extends \yii\web\AssetBundle
{
    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN,
    ];

    public $sourcePath = '@app/tests/mocha/assets/vendor';
    public $js = [ 'sinon-1.12.2.js' ];
}
