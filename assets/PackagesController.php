<?php

namespace app\assets;

use yii\console\Controller;
use yii\helpers\Json;
use yii\di\Instance;

/**
 * This command returns information about asset bundles for gulp/grunt.
 */
class PackagesController extends Controller
{
    /**
     * @var string $configPath to main config file
     */
    public $configPath;

    /**
     * @var string|array|\app\assets\BundleManager bundle manger component
     */
    public $bundleManager = 'bundleManager';

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['configPath'] // global for all actions
        );
    }

    public function init()
    {
        parent::init();

        $this->bundleManager = Instance::ensure($this->bundleManager, BundleManager::className());
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (isset($this->configPath)) {
            $this->bundleManager->configPath = $this->configPath;
        }

        return true;
    }

    /**
     * This command checks if any of the package files are newer than their directory,
     * and touches the directory to force Yii to publish it again.
     */
    public function actionDeploy()
    {
        $directories = $this->bundleManager->deploy();

        foreach ($directories as $directory) {
            echo "Touched $directory\n";
        }
    }

    /**
     * This command returns information about asset bundles for gulp/grunt.
     */
    public function actionIndex()
    {
        $ret = $this->bundleManager->getBundleInfo();

        echo Json::encode($ret);
    }
}
