<?php

namespace app\modules\legacyapi\controllers;

use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\models\db\User;
use Yii;
use app\models\db\City;
use app\models\db\ReportCategory;
use app\models\forms\ReportForm;
use app\modules\legacyapi\components\ApiController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class SubmitController extends ApiController
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
        $behaviors['appAuthenticator']['enabled'] = true;
        $behaviors['userAuthenticator']['enabled'] = Yii::$app->request->post('email', false) === false;

        return $behaviors;
    }

    private function getAuthenticatedUser()
    {
        $email = Yii::$app->request->getAuthUser();
        $password = Yii::$app->request->getAuthPassword();

        if ($email === null || $password === null) {
            return null;
        }

        $user = User::findOne([
            'email' => $email,
            'status' => User::STATUS_ACTIVE,
        ]);

        if ($user === null) {
            return null;
        }

        if ($user->validatePassword($password)) {
            return $user;
        }

        return null;
    }

    /**
     * Saves a Report to the database.
     *
     * @return bool|array
     * @throws Exception
     * @throws HttpException
     */
    public function actionIndex()
    {
        $this->enableCsrfValidation = false;

        $nadpis = Yii::$app->request->post('nadpis', ''); /* title of the complaint (required, max_length[64]) */
        $description = Yii::$app->request->post('description', ''); /* - description of the complaint (required) */
        $lat = Yii::$app->request->post('lat', ''); /* - GPS coordinates (required) */
        $lng = Yii::$app->request->post('lng', ''); /* - GPS coordinates (required) */
        $kategoria = Yii::$app->request->post('kategoria', ''); /* - id category of the complaint (required, see #kategoria) */
        $mesto = Yii::$app->request->post('mesto', ''); /* - id of the city */
        $accuracy = Yii::$app->request->post('accuracy', ''); /* - accuracy of GPS location in meters (optional) */
        $meno = Yii::$app->request->post('meno', ''); /* - name of the user (required if not sending HTTP BASIC auth parameters) */
        $email = Yii::$app->request->post('email', ''); /* - email of the user (required if not sending HTTP BASIC auth parameters) */
        $priezvisko = Yii::$app->request->post('priezvisko', 'myProject'); /* - surname of user */

        if (empty($nadpis)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_NADPIS);
        }

        if (empty($description)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_DESCRIPTION);
        }

        if (empty($lat) || empty($lng)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_LAT_LNG);
        }

        if (empty($kategoria)) {
            return static::setResponseData(self::ERROR_EMPTY_FIELD_KATEGORIA);
        }

        $category = ReportCategory::findOne(['id' => $kategoria]);

        if (!$category) {
            return static::setResponseData(self::ERROR_NO_CATEGORY_FOUND_BY_ID);
        }

        $user_location = $address = $street_name = $post_code = null;

        // https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=YOUR_API_KEY
        $apiResponse = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&key=' . Yii::$app->params['google']['api_key_server']);
        if ($apiResponse !== false) {
            $apiResponse = Json::decode($apiResponse);

            // postCode is the last item in this various size array
            $postCodePosition = count(ArrayHelper::getValue($apiResponse, 'results.0.address_components', [])) - 1;

            $user_location = $address = ArrayHelper::getValue($apiResponse, 'results.0.formatted_address');
            $street_name = ArrayHelper::getValue($apiResponse, 'results.0.address_components.1.long_name');
            $post_code = ArrayHelper::getValue($apiResponse, 'results.0.address_components.' . $postCodePosition . '.long_name');
            $mesto = ArrayHelper::getValue($apiResponse, 'results.0.address_components.3.long_name');

            if ($user_location === null || $street_name === null || $post_code === null || $mesto === null) {
                return static::setResponseData(self::ERROR_INVALID_LAT_OR_LONG);
            }
        }

        $city = City::findOne(['name' => $mesto]);
        if (!$city) {
            $city = City::getNearestCity($lat, $lng);
            Yii::warning('[legacyapi/submit] City can not be found in database:' . $mesto . '\nUsing nearest city:' . ($city ? $city->name : 'null'));
            // return static::setResponseData(self::ERROR_NO_CITY_FOUND_BY_ID);
        }

        $user = $this->getAuthenticatedUser();

        if ($user === null && User::findByEmail($email) !== null) {
            return static::setResponseData(self::ERROR_LOGIN_REQUIRED);
        }

        if ($user === null && (empty($meno) || empty($email))) {
            return static::setResponseData(self::ERROR_LOGIN_REQUIRED);
        }

        $data = [
            'status' => Report::STATUS_NEW,
            'nameFirst' => ArrayHelper::getValue($user, 'first_name', $meno),
            'nameLast' => ArrayHelper::getValue($user, 'last_name', $priezvisko),
            'email' => ArrayHelper::getValue($user, 'email', $email),
            'name' => $nadpis,
            'description' => $description,
            'latitude' => $lat,
            'longitude' => $lng,
            'city_id' => $city->id,
            'report_category_id' => $category->id,
            'user_location' => $user_location,
            'street_name' => $street_name,
            'address' => $address,
            'post_code' => $post_code,
            'anonymous' => 0,
            'zoom' => 16,
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new ReportForm($data);
            $model->setScenario(ReportForm::SCENARIO_API);
            $model->setUserId();
            if (!$model->save()) {
                throw new HttpException(400, strip_tags(Html::errorSummary($model)));
            }

            // Count # of uploaded files in array
            $total = count(ArrayHelper::getValue($_FILES, 'image.name', []));
            if ($total === 0) {
                $transaction->commit();

                return static::setResponseData(self::SUCCESSFUL_SUBMIT);
            }

            // Loop through each file
            for ($i = 0; $i < $total; $i++) {
                //Get the temp file path
                $tmpFilePath = $_FILES['image']['tmp_name'][$i];

                //Make sure we have a filepath
                if ($tmpFilePath != '') {
                    //Setup our new file path
                    $fileName = $_FILES['image']['name'][$i];
                    $newFilePath = Yii::getAlias(self::REPORT_IMAGE_PATH_TEMP . $fileName);

                    $data = file_get_contents($tmpFilePath);
                    $image = imagecreatefromstring($data);

                    //Upload the file into the temp dir
                    if ($image !== false && imagejpeg($image, $newFilePath, ReportAttachment::IMAGE_QUALITY)) {
                        $attachment = $model->constructAttachment(ReportAttachment::TYPE_PICTURE, [
                            'name' => $fileName,
                        ]);

                        if (!$attachment->save()) {
                            throw new Exception('Failed to save attached image!');
                        }
                    }
                }
            }

            $transaction->commit();
            return static::setResponseData(self::SUCCESSFUL_SUBMIT);
        } catch (Exception $e) {
            $transaction->rollBack();
            return static::setResponseData(self::ERROR_OTHER, $e->getMessage());
        }
    }
}
