<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PdfAsset extends AssetBundle
{
    /**
     * @var string the base directory for development assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $devPath = '@app/assets/pdf/src';

    /**
     * @var string the base directory for production assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $distPath = '@app/assets/pdf/dist';

    /**
     * @var string relative path to images
     *
     * Images in this directory will be optimized and copied to the production path
     * by the build process
     */
    public $imgPath = 'images';

    /**
     * @var string relative path to fonts
     *
     * Files in this directory will be copied to the production path
     * by the build process
     */
    public $fontPath = null;

    /**
     * @var array relative paths to css source files (sass, less etc.)
     *
     * files in these directory will be compiled to css files in the css directory
     */
    public $cssSourcePaths = ['sass'];

    /**
     * @var array list of production css files
     */
    public $css = [
        'css/style.css',
    ];

    /**
     * @var array|null list of development css files
     * If this is not null, it will overwrite $css
     */
    public $devCss = null;

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
    public $devJs = [];

    /**
     * @var array list of production js files
     */
    public $js = [];

    /**
     * @var array list of dependent asset bundles
     */
    public $depends = [];

    /**
     * @var array|null extra parameters to pass to gulp.
     */
    public $extraParams;
    /*
    public $extraParams = [
        'ignoreErrors' => true, // for third party plugins: don't run linter
    ];
    */
}
