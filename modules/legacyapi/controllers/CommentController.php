<?php

namespace app\modules\legacyapi\controllers;

use app\models\db\Report;
use app\models\db\User;
use app\models\forms\CommentForm;
use Yii;
use app\modules\legacyapi\components\ApiController;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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
        $behaviors['appAuthenticator']['enabled'] = true;

        return $behaviors;
    }

    public function actionIndex()
    {
        $this->enableCsrfValidation = false;

        $id = Yii::$app->request->post('id', ''); /* Id of the complaint */
        $message = Yii::$app->request->post('message', ''); /* comment of message */

        if (empty($id)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_ID);
        }

        if (empty($message)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_MESSAGE);
        }

        $report = Report::findOne(['id' => $id]);

        if ($report === null) {
            return static::setResponseData(self::ERROR_INVALID_REPORT_ID);
        }

        $comment = new CommentForm(['comment' => $message]);
        $comment->report = $report;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $myImage = UploadedFile::getInstanceByName('update');

            if ($myImage !== null) {
                if ($myImage->saveAs(Yii::getAlias(self::REPORT_IMAGE_PATH_TEMP . $myImage->name)) !== true) {
                    throw new Exception('Unable to save image!');
                } else {
                    $comment->pictures[] = $myImage->name;
                }
            }

            if (!$comment->handleComment()) {
                throw new BadRequestHttpException('Unable to save comment : ' . VarDumper::dumpAsString($comment->errors));
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }

        return static::setResponseData(self::SUCCESSFUL_COMMENT);
    }
}
