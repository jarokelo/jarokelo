<?php

namespace app\assets;

use yii\validators\ValidationAsset;

class CustomFormAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/custom_form/compressed.min.js' => [
            'js/custom_form/handler.js',
            'js/custom_form/forms.js',
        ],
    ];

    public $js = [
        'js/custom_form/compressed.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        ValidationAsset::class,
    ];

    public $extraParams;
}
