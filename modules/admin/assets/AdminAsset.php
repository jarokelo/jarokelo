<?php

namespace app\modules\admin\assets;

use app\assets\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $devPath = '@app/modules/admin/assets/main/src';
    public $distPath = '@app/modules/admin/assets/main/dist';

    /**
     * @var string relative path to images
     *
     * Images in this directory will be optimized and copied to the production path
     * by the build process
     */
    public $imgPath = 'svg';

    public $scssPath = 'scss';
    public $css = [
        'css/admin.css',
    ];
    public $devJs = [
        'js/admin.min.js' => [
            'js/admin._.js',
            'js/admin.city.js',
            'js/admin.comment.js',
            'js/admin.helper.js',
            'js/admin.modal.js',
            'js/admin.report.js',
            'js/admin.quicksearch.js',
            'js/admin.websocket.js',
            'js/admin.darkroom.plugin.box.js',
        ],
    ];
    public $js = [
        'js/admin.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\modules\admin\assets\BowerAsset',
        'app\modules\admin\assets\DropzoneAsset',
        'app\assets\PasswordValidatorAsset',
    ];
}
