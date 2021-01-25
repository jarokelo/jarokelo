<?php

namespace app\controllers\dropzone;

use Yii;
use yii\base\Action;

class RemoveAction extends Action
{
    public $uploadDest = '@runtime/upload-tmp/';
    public $uploadThumbDest = false;

    public function run()
    {
        $fileName = Yii::$app->request->post('fileName');

        if ($fileName === null) {
            return false;
        }

        if ($this->uploadThumbDest !== false && is_file(Yii::getAlias($this->uploadThumbDest) . $fileName)) {
            unlink(Yii::getAlias($this->uploadThumbDest) . $fileName);
        }
        if ($this->uploadDest !== false && is_file(Yii::getAlias($this->uploadDest) . $fileName)) {
            unlink(Yii::getAlias($this->uploadDest) . $fileName);
        }

        return true;
    }
}
