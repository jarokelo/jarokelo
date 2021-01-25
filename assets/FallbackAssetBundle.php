<?php

namespace app\assets;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * FallbackAssetBundle supports using a different asset bundle's
 * javascript files if the main asset bundle's js file fails to load.
 */
class FallbackAssetBundle extends AssetBundle
{
    /**
     * @var string classname of the fallback asset bundle
     */
    public $fallback = null;
    /**
     * @var string javascript expression that should be falsy if the main asset failed to load
     */
    public $check = null;

    /**
     * {@inheritdoc}
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        if ($this->fallback !== null && $this->check !== null) {
            $am = $view->getAssetManager();
            $fallback = $am->getBundle($this->fallback);
            $scripts = '';
            $jsOptions = $fallback->jsOptions;
            $cssOptions = $fallback->cssOptions;
            unset($jsOptions['depends']);
            unset($cssOptions['depends']);
            unset($cssOptions['condition']);
            unset($cssOptions['noscript']);
            if (!isset($cssOptions['rel'])) {
                $cssOptions['rel'] = 'stylesheet';
            }
            foreach ($fallback->js as $js) {
                $scripts .= Html::jsFile($am->getAssetUrl($fallback, $js), $fallback->jsOptions);
            }

            $styles = [];
            foreach ($fallback->css as $css) {
                $styles[] = [
                    'file' => $am->getAssetUrl($fallback, $css),
                    'options' => $cssOptions,
                ];
            }

            $position = isset($this->jsOptions['position']) ? $this->jsOptions['position'] : View::POS_END;

            if (count($styles)) {
                $view->jsFiles[$position][] = Html::script(
                    "
                        (function() {
                            if (!{$this->check}) {
                                var files = " . Json::htmlEncode($styles, 0) . ";

                                for (var i = 0, l = files.length; i < l; i++) {
                                    var tag = document.createElement('link');
                                    for (var opt in files[i].options) {
                                        tag[opt] = files[i].options[opt];
                                    }
                                    tag.href = files[i].file;
                                    document.head.appendChild(tag);
                                }
                            }
                        })();
                    ",
                    ['type' => 'text/javascript']
                );
            }

            $view->jsFiles[$position][] = Html::script(
                $this->check . ' || document.write(' . Json::htmlEncode($scripts, 0) . ');',
                ['type' => 'text/javascript']
            );
        }
    }
}
