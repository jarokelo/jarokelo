<?php

namespace app\modules\api;

use Yii;
use yii\helpers\Url;

class Module extends \yii\base\Module
{
    public $layout = null;

    public $defaultRoute = 'default';

    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->setComponents($this->components);
    }
}
