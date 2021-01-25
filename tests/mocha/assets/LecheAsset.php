<?php

namespace app\tests\mocha\assets;

class LecheAsset extends \yii\web\AssetBundle
{
    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN,
    ];

    public $sourcePath = '@app/tests/mocha/assets/vendor';
    public $js = [ 'leche-2.0.0.js' ];
}
