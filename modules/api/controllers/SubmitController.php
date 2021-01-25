<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\models\db\User;
use app\models\db\City;
use app\models\db\ReportCategory;
use app\models\forms\ReportForm;
use app\modules\api\components\ApiController;
use yii\base\Exception;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\UploadedFile;

class SubmitController extends ApiController
{
    const REPORT_IMAGE_PATH_TEMP = '@runtime/upload-tmp/report/';

    /**
     * It's hard-coded currently
     * @var int
     */
    const ZOOM_VALUE = 16;

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['POST', 'HEAD'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['userAuthenticator']['enabled'] = true;
        return $behaviors;
    }

    /**
     * Saves a Report to the database.
     *
     * @return bool
     * @throws Exception
     * @throws HttpException
     */
    public function actionIndex()
    {
        $city_id = Yii::$app->request->post('city_id');
        $category_id = Yii::$app->request->post('category_id');
        $latitude = Yii::$app->request->post('latitude');
        $longitude = Yii::$app->request->post('longitude');
        $name = Yii::$app->request->post('name');
        $description = Yii::$app->request->post('description');
        $user_location = Yii::$app->request->post('user_location');
        $street_name = Yii::$app->request->post('street_name');
        $address = Yii::$app->request->post('address');
        $post_code = Yii::$app->request->post('post_code');
        $anonymous = Yii::$app->request->post('anonymous');

        $city = City::findOne(['id' => $city_id]);
        $category = ReportCategory::findOne(['id' => $category_id]);

        if (!$name) {
            throw new HttpException(400, 'Invalid name');
        }

        if (!$description) {
            throw new HttpException(400, 'Invalid description');
        }

        if (!$user_location) {
            throw new HttpException(400, 'Invalid user location');
        }

        if (!$street_name) {
            throw new HttpException(400, 'Invalid street name');
        }

        if (!$address) {
            throw new HttpException(400, 'Invalid address');
        }

        if (!$post_code) {
            throw new HttpException(400, 'Invalid post code');
        }

        if (!$category) {
            throw new HttpException(400, 'Invalid category id');
        }

        if (!is_numeric($latitude)) {
            throw new HttpException(400, 'Invalid latitude value');
        }

        if (!is_numeric($longitude)) {
            throw new HttpException(400, 'Invalid longitude value');
        }

        if (!$city) {
            // Select the closest city
            $city = City::getNearestCity($latitude, $longitude);
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        $data = ['ReportForm' => [
            'status' => Report::STATUS_NEW,
            'user_id' => $user->id,
            'nameFirst' => $user->first_name,
            'nameLast' => $user->last_name,
            'email' => $user->email,
            'name' => $name,
            'description' => $description,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city_id' => $city_id,
            'report_category_id' => $category_id,
            'user_location' => $user_location,
            'street_name' => $street_name,
            'address' => $address,
            'post_code' => $post_code,
            'anonymous' => $anonymous,
            'zoom' => self::ZOOM_VALUE,
        ]];

        $model = new ReportForm();
        $model->setScenario(Report::SCENARIO_API);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$model->load($data) || !$model->save()) {
                throw new HttpException(400, strip_tags(Html::errorSummary($model)));
            }

            $model->addActivity(ReportActivity::TYPE_OPEN, ['user_id' => $model->user_id]);

            $myImage = UploadedFile::getInstanceByName('image');

            if ($myImage === null) {
                $transaction->commit();
                return true;
            }

            $fileName = $myImage->baseName . '.' . $myImage->extension;

            if ($myImage->saveAs(Yii::getAlias(self::REPORT_IMAGE_PATH_TEMP) . $fileName) !== true) {
                throw new Exception('Unable to save image!');
            }

            $attachment = $model->constructAttachment(ReportAttachment::TYPE_PICTURE, [
                'name' => $fileName,
            ]);

            if (!$attachment->save()) {
                throw new Exception('Failed to save attached image!');
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }
}
