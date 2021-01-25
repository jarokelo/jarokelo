<?php

namespace app\assets;

use Yii;
use yii\web\View;

/**
 *
 */
class MapboxCommonAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $devPath = '@app/assets/main/src';

    /**
     * @var string
     */
    public $distPath = '@app/assets/main/dist';

    /**
     * @var array
     */
    public $css = [
        'https://api.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css',
    ];

    /**
     * @var array
     */
    public $devJs = [
        'https://api.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js',
    ];

    /**
     * @var array
     */
    public $js = [
        'https://api.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @var array|null
     */
    public $extraParams;

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->getView()->registerJs(
            sprintf(
                'window.mapboxToken="%s";',
                Yii::$app->params['map']['mapboxToken']
            ),
            View::POS_HEAD
        );
        parent::init();
    }
}
