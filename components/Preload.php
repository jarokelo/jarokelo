<?php
namespace app\components;

use Yii;
use \yii\base\Component;

class Preload extends Component
{
    public function init()
    {
        parent::init();
        static::setUploaderWidgetLabels();
    }

    public static function setUploaderWidgetLabels()
    {
        if (Yii::$app instanceof \yii\web\Application) {
            if (Yii::$app->session->has('lang')) {
                Yii::$app->language = Yii::$app->session->get('lang');
            }
            Yii::$container->set('app\components\jqueryupload\UploadWidget', [
                'strings' => [
                    'upload-label' => Yii::t('button', 'upload'),
                    'delete-label' => Yii::t('button', 'delete'),
                    'cancel-label' => Yii::t('button', 'cancel'),
                    'retry-label' => Yii::t('button', 'retry'),
                    'upload-failed' => Yii::t('label', 'upload.failed'),
                ],
            ]);
        }
    }
}
