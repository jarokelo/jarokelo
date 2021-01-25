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
class PasswordValidatorAsset extends AssetBundle
{
    /**
     * @var string the base directory for development assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $devPath = '@app/assets/main/src';

    /**
     * @var string the base directory for production assets
     *
     * You can use either a directory or an alias of the directory.
     */
    public $distPath = '@app/assets/main/dist';

    /**
     * @var array list of paths (relative to base directory) to copy from development directory to production directory on build
     */
    public $otherPaths = [];

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
        'js/pvalidator.js' => [
            'js/validate.js',
        ],
    ];

    /**
     * @var array list of production js files
     */
    public $js = [
        'js/pvalidator.js',
    ];

    /**
     * @var array list of dependent asset bundles
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}
