<?php

namespace app\assets;

use Yii;
use \yii\helpers\FileHelper;

/**
 * Modified version of AssetManager.
 * Places asset versions together into subdirectory.
 */
class AssetManager extends \yii\web\AssetManager
{
    protected function hash($path)
    {
        if (is_callable($this->hashCallback)) {
            return call_user_func($this->hashCallback, $path);
        }
        $time = filemtime($path);
        $path = is_file($path) ? dirname($path) : $path;
        // use /, not DIRECTORY_SEPARATOR, because this is used in urls too,
        // and / works everywhere as a directory separator
        $pathHash = sprintf('%x', crc32($path));
        $baseDir = $this->basePath . DIRECTORY_SEPARATOR . $pathHash;
        if (!is_dir($baseDir)) {
            FileHelper::createDirectory($baseDir, $this->dirMode, true);
        }
        return sprintf('%s/%x', $pathHash, crc32(Yii::getVersion() . $time));
    }

    /**
     * Clean up old asset versions.
     * @param string|null $path assets path or alias, if null, Yii::$app->assetManager->basePath is used.
     * @param integer $keep how many old versions to keep
     */
    public static function cleanup($path = null, $keep = 0)
    {
        $keep++;
        if ($path === null) {
            $path = Yii::$app->assetManager->basePath;
        }
        $path = Yii::getAlias($path);
        if (!is_dir($path)) {
            return;
        }

        $dir = opendir($path);

        while (false !== ($item = readdir($dir))) {
            if ($item === '.' || $item === '..' || !is_dir($path . DIRECTORY_SEPARATOR . $item) || is_link($path . DIRECTORY_SEPARATOR . $item)) {
                continue;
            }
            $subdir = opendir($path . DIRECTORY_SEPARATOR . $item);
            $versions = [];
            while (false !== ($version = readdir($subdir))) {
                if ($version === '.' || $version === '..') {
                    continue;
                }
                $versionPath = $path . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . $version;
                $stat = lstat($versionPath);
                $versions[$versionPath] = $stat['mtime'];
            }
            arsort($versions);
            $versions = array_slice($versions, $keep);
            foreach ($versions as $version => $time) {
                if (!is_link($version) && is_dir($version)) {
                    FileHelper::removeDirectory($version);
                } else {
                    @unlink($version);
                }
            }
            closedir($subdir);
        }
        closedir($dir);
    }
}
