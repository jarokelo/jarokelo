<?php

namespace app\assets;

/**
 *
 */
class MapboxAsset extends MapboxCommonAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->devJs = array_merge(
            $this->devJs,
            [
                'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.4/mapbox-gl-geocoder.min.js',
                'js/mapbox/map.min.js' => [
                    'js/mapbox/map.js',
                ],
            ]
        );

        $this->js = array_merge(
            $this->js,
            [
                'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.4/mapbox-gl-geocoder.min.js',
                'js/mapbox/map.min.js',
            ]
        );

        $this->css = array_merge(
            $this->css,
            [
                'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.4/mapbox-gl-geocoder.css',
            ]
        );
        parent::init();
    }
}
