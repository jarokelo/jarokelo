<?php

namespace app\modules\api\controllers;

use app\models\db\Report;
use app\models\forms\CommentForm;
use Yii;
use app\modules\api\components\ApiController;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\UploadedFile;

class CommentController extends ApiController
{
    const REPORT_IMAGE_PATH_TEMP = '@runtime/upload-tmp/report/';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['POST', 'HEAD'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['userAuthenticator']['enabled'] = true;

        return $behaviors;
    }

    public function actionIndex()
    {
        $report_id = Yii::$app->request->post('report_id');
        $message = Yii::$app->request->post('message', '');

        $report = Report::findOne(['id' => $report_id]);

        if ($report === null) {
            throw new HttpException(400, 'Invalid report id');
        }

        if (empty($message)) {
            throw new HttpException(400, 'Missing comment message');
        }

        $comment = new CommentForm(['comment' => $message]);
        $comment->report = $report;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $myImage = UploadedFile::getInstanceByName('image');

            if ($myImage !== null) {
                $fileName = $myImage->baseName . '.' . $myImage->extension;

                if ($myImage->saveAs(self::REPORT_IMAGE_PATH_TEMP . $fileName) !== true) {
                    throw new Exception('Unable to save image!');
                } else {
                    $comment->pictures[] = $fileName;
                }
            }

            if (!$comment->handleComment()) {
                throw new BadRequestHttpException('Unable to save comment : ' . VarDumper::dumpAsString($comment->errors));
            };
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }

        return true;
    }
}
