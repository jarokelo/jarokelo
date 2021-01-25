<?php

namespace app\components\jqueryupload;

class BaseAction extends \yii\base\Action
{
    /**
     * @var string prefix for all upload actions
     */
    protected $actionPrefix;

    public function cleanFilename($name)
    {
        return basename(stripslashes($name));
    }

    public function cleanUploadFilename($name)
    {
        $name = trim(basename(stripslashes($name)), ".\x00..\x20");

        if (!$name) {
            $name = str_replace('.', '-', microtime(true));
        }

        // TODO: add extension based on mime
        /*if (strpos($name, '.') === false) {

        }*/

        // TODO: fix wrong extension

        return $name;
    }

    public function beforeRun()
    {
        if (($pos = strpos($this->id, '.')) === false) {
            $this->actionPrefix = '';
        } else {
            $this->actionPrefix = substr($this->id, 0, $pos + 1);
        }

        return parent::beforeRun();
    }
}
