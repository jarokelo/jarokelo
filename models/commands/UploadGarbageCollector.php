<?php

namespace app\models\commands;

use Yii;
use yii\base\Model;

class UploadGarbageCollector extends Model
{
    /**
     * folders to check for old files recursively
     *
     * @var array
     */
    private $_folders = [
        '@runtime/upload-tmp',
    ];

    const OLDER_THAN_DAYS = 1;

    public function process()
    {
        foreach ($this->_folders as $folder) {
            $files = $this->getDirContents(Yii::getAlias($folder));
            foreach ($files as $file) {
                $createdAt = filemtime($file);
                $criteria = strtotime('-' . self::OLDER_THAN_DAYS . 'days');
                if ($createdAt <= $criteria) {
                    unlink($file);
                }
            }
        }
    }

    private function getDirContents($dir, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = $dir . DIRECTORY_SEPARATOR . $value;
            $realPath = realpath($path);
            if (!is_dir($realPath)) {
                $results[] = $path;
            } else {
                if ($value != '.' && $value != '..') {
                    $this->getDirContents($path, $results);
                }
            }
        }

        return $results;
    }
}
