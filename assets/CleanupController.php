<?php

namespace app\assets;

use Yii;
use yii\console\Controller;

/**
 * Deletes old published assets.
 */
class CleanupController extends Controller
{
    /**
     * @var integer number of old asset versions to keep.
     */
    public $keep = 0;

    /**
     * @var string assets base path, relative to Yii::$app->basePath
     */
    public $assets = 'web/assets';

    public function actionIndex()
    {
        $assets = realpath(Yii::$app->basePath . '/' . trim($this->assets, '/'));
        if ($assets === false || !is_dir($assets)) {
            $this->usageError('assets path is invalid.');
        }
        AssetManager::cleanup($assets, $this->keep);
    }

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['assets', 'keep'] // global for all actions
        );
    }
}
