<?php

namespace app\assets;

/**
 *
 */
class MapLayerAsset extends MapboxCommonAsset
{
    /**
     * @var array
     */
    public $devJs = [
        'js/mapbox/jszip.min.js' => [
            'js/mapbox/jszip.min.js',
        ],
        'js/mapbox/jszip-utils.min.js' => [
            'js/mapbox/jszip-utils.min.js',
        ],
    ];

    /**
     * @var array
     */
    public $js = [
        'js/mapbox/jszip.min.js',
        'js/mapbox/jszip-utils.min.js',
    ];

    /**
     * @var array
     */
    public $css = [];

    /**
     * @var array
     */
    public $depends = [];
}
