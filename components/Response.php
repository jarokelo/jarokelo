<?php

namespace app\components;

use Yii;

/**
 * The Response class represents an HTTP response.
 *
 * This class overrides redirect to disable the special ajax handling by default.
 */
class Response extends \yii\web\Response
{
    /**
     * @inheritdoc
     */
    public function redirect($url, $statusCode = 302, $checkAjax = false)
    {
        return parent::redirect($url, $statusCode, $checkAjax);
    }
}
