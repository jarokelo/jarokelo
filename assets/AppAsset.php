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
class AppAsset extends AssetBundle
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
    public $fontPath = 'fonts';

    /**
     * @var array list of paths (relative to base directory) to copy from development directory to production directory on build
     */
    public $otherPaths = [];

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
    public $devJs = [
        'js/combined.js' => [
            'js/alert.scroll.js',
            'js/base.js',
            'js/helper.js',
            'js/upload.js',
            'js/donate.js',
            'js/navigation.js',
            'js/report.js',
            'js/report.create.js',
            'js/report.scroll.js',
            'js/report.search.js',
            'js/report.view.js',
            'js/report.comments.js',
//            'js/reportsonmap.js',
            'js/profile.js',
            'js/statistics.js',
            'js/rss.js',
            'js/share.js',
            'js/input.js',
            'js/tabs.js',
            'js/accordion.js',
            'js/geolocation.js',
            'js/collapse.js',
            'js/loader.js',
            'js/filter-toggler.js',
            'js/api.scroll.js',
            'js/stat.scroll.js',
            'js/widget.configure.js',
            'js/pr-page.js',
            'js/news.view.js',
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
        'app\assets\BowerAsset',
        'app\assets\ModalAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];

    /**
     * @var array|null extra parameters to pass to gulp.
     */
    public $extraParams;
    // public $extraParams = [
    //     // 'ignoreErrors' => true, // for third party plugins: don't run linter
    //     // 'customPaths' => [
    //     //     'keyName' => [
    //     //         'sources' => ['path/**/*.*'],
    //     //         'dist' => 'dist-path',
    //     //         'params' => []
    //     //     ]
    //     // ]
    // ];
}
