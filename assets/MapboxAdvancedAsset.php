<?php

namespace app\assets;

/**
 *
 */
class MapboxAdvancedAsset extends MapboxCommonAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->devJs = array_merge(
            $this->devJs,
            [
                'js/mapbox/reportsonmap.min.js' => [
                    'js/mapbox/reportsonmap.js',
                ],
            ]
        );

        $this->js = array_merge(
            $this->js,
            [
                'js/mapbox/reportsonmap.min.js',
            ]
        );

        $this->depends = array_merge(
            $this->depends,
            [MobileDetect::class]
        );
        parent::init();
    }
}
