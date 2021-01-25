<?php

namespace app\assets;

class BowerAsset extends AssetBundle
{
    public $devPath = '@bower';
    public $distPath = '@app/assets/vendor/dist';

    public $js = [
        'js/vendor.js',
    ];

    public $devJs = [
        'js/vendor.js' => [
            'dropzone/dist/dropzone.js',
            'autosize/dist/autosize.min.js',
            'svg4everybody/dist/svg4everybody.min.js',
            'lightgallery/dist/js/lightgallery.min.js',
            'lightgallery/dist/js/lg-video.min.js',
            'lightgallery/dist/js/lg-zoom.min.js',
            'lightgallery/dist/js/lg-thumbnail.min.js',
            'owl.carousel/dist/owl.carousel.min.js',
            'owl.carousel2.thumbs/dist/owl.carousel2.thumbs.min.js',
            'malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
        ],
    ];

    public $extraParams = [
        'ignoreErrors' => true,
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\BowerCssAsset',
    ];
}
