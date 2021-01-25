<?php

namespace app\assets;

/**
 *
 */
class MapboxLayerAsset extends MapboxCommonAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->devJs = array_merge(
            $this->devJs,
            [
                'js/mapbox/parser.min.js' => [
                    'js/mapbox/parser.js',
                ],
                'js/mapbox/togeojson.min.js' => [
                    'js/mapbox/togeojson.js',
                ],
            ]
        );

        $this->js = array_merge(
            $this->js,
            [
                'js/mapbox/parser.min.js',
                'js/mapbox/togeojson.min.js',
            ]
        );
        parent::init();
    }
}
