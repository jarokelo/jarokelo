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
class ModalAsset extends AssetBundle
{
    /**
     * @var string the base directory for development assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $devPath = '@app/assets/jquery-modal/src';

    /**
     * @var string the base directory for production assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $distPath = '@app/assets/jquery-modal/dist';

    /**
     * @var array relative paths to css source files (sass, less etc.)
     *
     * files in these directory will be compiled to css files in the css directory
     */
    public $cssSourcePaths = false;

    /**
     * @var array list of production css files
     */
    public $css = [];

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
    public $devJs = [
        'js/combined.js' => [
            'js/modal.js',
        ],
    ];

    /**
     * @var array list of production js files
     */
    public $js = [
        'js/combined.js',
    ];

    /**
     * @var array list of dependent asset bundles
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @var array|null extra parameters to pass to gulp.
     */
    public $extraParams = [
        'ignoreErrors' => true, // for third party plugins: don't run linter
    ];
}
