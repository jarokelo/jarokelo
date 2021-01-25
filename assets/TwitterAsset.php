<?php

namespace app\assets;

/**
 * This asset bundle provides the [Twitter SDK](https://dev.twitter.com/web/javascript)
 */
class TwitterAsset extends FallbackAssetBundle
{
    public $sourcePath = null;

    public $devJs = [
        '//platform.twitter.com/widgets.js',
    ];

    public $js = [
        '//platform.twitter.com/widgets.js',
    ];

    public $jsOptions = [
        'async' => 'async',
        'defer' => 'defer',
    ];
}
