<?php

namespace app\assets;

use yii\web\AssetBundle as BaseAssetBundle;

/**
 * Modified version of Yii's AssetBundle with support for
 * separate development and production assets
 */
class AssetBundle extends BaseAssetBundle
{
    /**
     * @var array list of development js files
     * If this is not null, it will overwrite $js
     * If an element is an array, the javascript files in that array
     * will be compiled to the javascript file specified in the element's key
     *
     * For example:
     *
     * ```php
     * public $devJs = [
     *     'js/combined.js' => [ 'js/file1.js', 'js/file2.js' ],
     * ];
     * ```
     *
     */
    public $devJs = null;
    /**
     * @var array|null list of development css files
     * If this is not null, it will overwrite $css
     */
    public $devCss = null;
    /**
     * @var string the base directory for development assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $devPath = null;
    /**
     * @var string the base directory for production assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $distPath = null;
    /**
     * @var string relative path to images
     *
     * Images in this directory will be optimized and copied to the production path
     * by the build process
     */
    public $imgPath = null;
    /**
     * @var string relative path to fonts
     *
     * Files in this directory will be copied to the production path
     * by the build process
     */
    public $fontPath = null;
    /**
     * @var array list of paths (relative to base directory) to copy from development directory to production directory on build
     */
    public $otherPaths = [];
    /**
     * @var string relative path to scss files
     *
     * files in this directory will be compiled to css files in the css directory
     * @deprecated use $cssSourcePaths instead
     */
    public $scssPath = null;

    /**
     * @var array relative paths to css source files (scss, less etc.)
     *
     * files in these directory will be compiled to css files in the css directory
     */
    public $cssSourcePaths = [];

    /**
     * @var array|null extra parameters to pass to gulp.
     */
    public $extraParams;

    /**
     * @var string|boolean|null
     * If this is true, it will be overridden by devPath or distPath.
     * If it is null, it won't, so that a dummy bundle can still be created from this bundle.
     */
    public $sourcePath = true;

    private $_distJs = [];

    private $_distCss = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->sourcePath === true) {
            if (YII_DEBUG) {
                $this->_distJs = $this->js;
                if ($this->devJs !== null) {
                    $this->js = [];
                    foreach ($this->devJs as $name => $scripts) {
                        if (is_array($scripts)) {
                            $this->js = array_merge($this->js, $scripts);
                        } else {
                            $this->js[] = $scripts;
                        }
                    }
                }
                $this->_distCss = $this->css;
                if ($this->devCss !== null) {
                    $this->css = $this->devCss;
                }
                if ($this->devPath !== null) {
                    $this->sourcePath = $this->devPath;
                }
            } else {
                if ($this->distPath !== null) {
                    $this->sourcePath = $this->distPath;
                }
            }
        }
        parent::init();
    }

    /**
     * Returns production js files, even if the environment is in debug mode (YII_DEBUG is true)
     * @return array js files
     */
    public function getDistJs()
    {
        if (YII_DEBUG) {
            return $this->_distJs;
        }
        return $this->js;
    }

    /**
     * Returns production css files, even if the environment is in debug mode (YII_DEBUG is true)
     * @return array css files
     */
    public function getDistCss()
    {
        if (YII_DEBUG) {
            return $this->_distCss;
        }
        return $this->css;
    }
}
