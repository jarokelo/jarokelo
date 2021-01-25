<?php

namespace app\controllers\dropzone;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\UploadedFile;

class UploadAction extends Action
{
    public $fileName = 'file';
    public $uploadDest = '@runtime/upload-tmp/';
    public $uploadThumbDest = false;

    public $afterUploadHandler = null;
    public $afterUploadData = null;

    protected $uploadDir = '';
    protected $uploadThumbDir = '';

    public function init()
    {
        parent::init();

        $this->uploadDir = Yii::getAlias($this->uploadDest);
        $this->uploadThumbDir = Yii::getAlias($this->uploadThumbDest);

        if (!is_dir($this->uploadDir)) {
            FileHelper::createDirectory($this->uploadDir);
        }
        if (!is_dir($this->uploadThumbDir)) {
            FileHelper::createDirectory($this->uploadThumbDir);
        }
    }

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->fileName);
        $fileThumb = UploadedFile::getInstanceByName($this->fileName);

        if (!$file || $file->hasError) {
            throw new HttpException(500, 'Upload error');
        }

        $fileName = $file->name;
        if (file_exists($this->uploadDir . $fileName)) {
            $fileName = $file->baseName . '-' . uniqid() . '.' . $file->extension;
        }

        $file->saveAs($this->uploadDir . $fileName, false);
        if ($this->uploadThumbDest) {
            $fileThumb->saveAs($this->uploadThumbDir . $fileName);
        }

        $response = [
            'filename' => $fileName,
        ];

        if (isset($this->afterUploadHandler)) {
            $data = [
                'data' => $this->afterUploadData,
                'file' => $file,
                'dirName' => $this->uploadDir,
                'src' => $this->uploadDir,
                'filename' => $fileName,
                'params' => Yii::$app->request->post(),
            ];

            if ($result = call_user_func($this->afterUploadHandler, $data)) {
                $response['afterUpload'] = $result;
            }
        }

        return Json::encode($response);
    }
}
