<?php

namespace app\components\jqueryupload;

class ThumbAction extends BaseAction
{
    /**
     * @var string temporary upload destination
     */
    public $uploadDest;
    /**
     * @var string thumbnail destination
     */
    public $thumbDest;

    /**
     * @var boolean whether to use thumbs directory
     */
    public $useThumbs = true;

    protected function getFileType($file_path)
    {
        switch (strtolower(pathinfo($file_path, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return 'application/octet-stream';
        }
    }

    public function run($filename)
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

        $filename = $this->cleanFilename($filename);

        $path = ($this->useThumbs ? $this->thumbDest : $this->uploadDest) . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            throw new \yii\web\NotFoundHttpException();
        }

        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . $this->getFileType($path));

        readfile($path);
    }
}
