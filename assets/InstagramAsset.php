<?php

namespace app\assets;

/**
 * This asset bundle provides the [Instagram SDK](https://developers.facebook.com/docs/instagram/oembed)
 */
class InstagramAsset extends FallbackAssetBundle
{
    public $sourcePath = null;

    public $devJs = [
        '//www.instagram.com/embed.js',
    ];

    public $js = [
        '//www.instagram.com/embed.js',
    ];

    public $jsOptions = [
        'async' => 'async',
        'defer' => 'defer',
    ];
}
