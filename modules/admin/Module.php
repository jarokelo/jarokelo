<?php

namespace app\modules\admin;

use app\models\db\Admin;
use app\components\helpers\Link;

use Yii;

use yii\console\Application as ConsoleApplication;
use yii\helpers\Url;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'task/index';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'app\modules\admin\commands';
            $this->defaultRoute = 'admin/create';
        } else {
            Yii::$app->errorHandler->errorAction = '/admin/auth/error';
            Yii::$app->homeUrl = Url::to(['/admin/task/index']);
        }


        Yii::$app->assetManager->bundles = array_merge(Yii::$app->assetManager->bundles, [
            'yii\bootstrap\BootstrapAsset' => [
                'css' => ['css/bootstrap.css'],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!(Yii::$app instanceof ConsoleApplication) && !Yii::$app->user->isGuest && Yii::$app->user->identity->status == Admin::STATUS_INACTIVE) {
            Yii::$app->user->logout();
            return false;
        }

        return true;
    }
}
