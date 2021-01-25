<?php

namespace app\assets;

class CookieConsentAsset extends AssetBundle
{
    public $devPath = '@app/assets/main/src';
    public $distPath = '@app/assets/main/dist';
    public $devJs = [
        'js/cookie/cookie-consent.min.js' => [
            'js/cookie/cookie-consent.min.js',
        ],
    ];

    public $js = [
        'js/cookie/cookie-consent.min.js',
    ];

    public $depends = [];

    public $extraParams;
}
