<?php

namespace app\components\jqueryupload;

class DeleteAction extends BaseAction
{
    /**
     * @var string temporary upload destination
     */
    public $uploadDest;
    /**
     * @var string thumbnail destination
     */
    public $thumbDest;

    public function run($filenames)
    {
        if ($this->uploadDest === null) {
            $this->uploadDest = \Yii::$app->runtimePath . DIRECTORY_SEPARATOR . 'temp';
        } else {
            $this->uploadDest = \Yii::getAlias($this->uploadDest);
        }
        if ($this->thumbDest === null) {
            $this->thumbDest = $this->uploadDest . DIRECTORY_SEPARATOR . 'thumb';
        } else {
            $this->thumbDest = \Yii::getAlias($this->thumbDest);
        }

        $files = [];

        if (!is_array($filenames)) {
            if ($filenames !== null && $filenames !== '') {
                $filenames = [$filenames];
            }
        }

        foreach ($filenames as $filename) {
            $filename = $this->cleanFilename($filename);
            $path = $this->uploadDest . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path)) {
                @unlink($path);
            }
            $path = $this->thumbDest . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}
