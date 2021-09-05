<?php

namespace app\modules\admin\assets;

use app\assets\AssetBundle;
use yii\jui\JuiAsset;

class CustomQuestionAsset extends AssetBundle
{
    public $devPath = '@app/modules/admin/assets/main/src';
    public $distPath = '@app/modules/admin/assets/main/dist';
    public $devJs = [
        'js/custom_question.min.js' => [
            'js/custom_question/question.js',
            'js/custom_question/templates.js',
        ],
    ];
    public $js = [
        'js/custom_question.min.js',
    ];
    public $depends = [
        JuiAsset::class,
    ];
}
