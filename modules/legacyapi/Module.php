<?php

namespace app\modules\legacyapi;

use Yii;
use yii\helpers\Url;

class Module extends \yii\base\Module
{
    public $layout = null;

    public $defaultRoute = 'default';

    public $controllerNamespace = 'app\modules\legacyapi\controllers';

    public function init()
    {
        parent::init();
        $this->setComponents($this->components);
    }
}
