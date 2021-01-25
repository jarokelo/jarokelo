<?php

namespace app\assets;

//use Yii;
use yii\web\AssetBundle;

class BowerCssAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $css = [
        'normalize-css/normalize.css',
        'dropzone/dist/basic.css',
        'dropzone/dist/dropzone.css',
        'lightgallery/dist/css/lightgallery.min.css',
        'owl.carousel/dist/assets/owl.carousel.min.css',
        'owl.carousel/dist/assets/owl.theme.default.min.css',
        'malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css',
    ];
//
//    /**
//     * @inheritdoc
//     */
//    public function init()
//    {
//        parent::init();
//
//        Yii::$app->assetManager->bundles = array_merge(Yii::$app->assetManager->bundles, [
//            'yii\bootstrap\BootstrapAsset' => [
//                'css' => ['css/bootstrap.css'],
//            ],
//        ]);
//    }
}
