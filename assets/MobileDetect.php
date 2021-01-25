<?php
namespace app\assets;

/**
 *
 */
class MobileDetect extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/mobile-detect.js' => [
            'js/mobile-detect.js',
        ],
    ];

    public $js = [
        'js/mobile-detect.js',
    ];
}
