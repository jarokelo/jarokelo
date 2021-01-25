<?php

namespace app\modules\legacyapi\components;

use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\PageCache;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use \yii\web\Response;

class ApiController extends Controller
{
    private static $errorMap = [
        self::ERROR_LOGIN_REQUIRED => [
            'code' => self::ERROR_LOGIN_REQUIRED,
            'message' => 'Login credentials required!',
        ],
        self::ERROR_LOGIN_WRONG_USERNAME_PASSWORD => [
            'code' => self::ERROR_LOGIN_WRONG_USERNAME_PASSWORD,
            'message' => 'Wrong username and password.',
        ],
        self::ERROR_LOGIN_WRONG_PASSWORD => [
            'code' => self::ERROR_LOGIN_WRONG_PASSWORD,
            'message' => 'Wrong password.',
        ],
        self::ERROR_INVALID_KEY_OR_EMPTY_FIELDS => [
            'code' => self::ERROR_INVALID_KEY_OR_EMPTY_FIELDS,
            'message' => 'Required fields left empty or is invalid - key!',
        ],
        self::ERROR_EMPTY_FIELD_ID => [
            'code' => self::ERROR_EMPTY_FIELD_ID,
            'message' => 'Required field left empty - id!',
        ],
        self::ERROR_EMPTY_FIELD_MESSAGE => [
            'code' => self::ERROR_EMPTY_FIELD_MESSAGE,
            'message' => 'Required field left empty - message!',
        ],
        self::ERROR_INVALID_REPORT_ID => [
            'code' => self::ERROR_INVALID_REPORT_ID,
            'message' => 'Invalid report id!',
        ],
        self::ERROR_EMPTY_FIELD_NADPIS => [
            'code' => self::ERROR_EMPTY_FIELD_NADPIS,
            'message' => 'Required field left empty - nadpis!',
        ],
        self::ERROR_EMPTY_FIELD_DESCRIPTION => [
            'code' => self::ERROR_EMPTY_FIELD_DESCRIPTION,
            'message' => 'Required field left empty - description!',
        ],
        self::ERROR_EMPTY_FIELD_LAT_LNG => [
            'code' => self::ERROR_EMPTY_FIELD_LAT_LNG,
            'message' => 'Required field left empty - lat or lng!',
        ],
        self::ERROR_EMPTY_FIELD_KATEGORIA => [
            'code' => self::ERROR_EMPTY_FIELD_LAT_LNG,
            'message' => 'Required field left empty - kategoria!',
        ],
        self::ERROR_EMPTY_FIELD_IMAGE => [
            'code' => self::ERROR_EMPTY_FIELD_IMAGE,
            'message' => 'Required field left empty - image!',
        ],
        self::ERROR_EMPTY_FIELD_MESTO => [
            'code' => self::ERROR_EMPTY_FIELD_MESTO,
            'message' => 'Required field left empty - mesto!',
        ],
        self::ERROR_NO_CITY_FOUND_BY_ID => [
            'code' => self::ERROR_NO_CITY_FOUND_BY_ID,
            'message' => 'Invalid city id!',
        ],
        self::ERROR_NO_CATEGORY_FOUND_BY_ID => [
            'code' => self::ERROR_NO_CATEGORY_FOUND_BY_ID,
            'message' => 'Invalid category id!',
        ],
        self::ERROR_INVALID_LAT_OR_LONG => [
            'code' => self::ERROR_INVALID_LAT_OR_LONG,
            'message' => 'invalid lat or lng!',
        ],
        self::ERROR_OTHER => [
            'code' => self::ERROR_OTHER,
            'message' => '',
        ],
    ];

    private static $successMap = [
        self::SUCCESSFUL_COMMENT => [
            'code' => self::SUCCESSFUL_COMMENT,
            'message' => 'Komentár bol pridaný.',
        ],
        self::SUCCESSFUL_SUBMIT => [
            'code' => self::SUCCESSFUL_SUBMIT,
            'message' => 'Submit successfull!',
        ],
    ];

    const ERROR_LOGIN_REQUIRED = -101;
    const ERROR_LOGIN_WRONG_USERNAME_PASSWORD = -102;
    const ERROR_LOGIN_WRONG_PASSWORD = -103;

    const ERROR_INVALID_KEY_OR_EMPTY_FIELDS = -300;
    const ERROR_INVALID_REPORT_ID = -301;

    const ERROR_EMPTY_FIELD_ID = -601;
    const ERROR_EMPTY_FIELD_MESSAGE = -602;
    const ERROR_EMPTY_FIELD_NADPIS = -603;
    const ERROR_EMPTY_FIELD_DESCRIPTION = -604;
    const ERROR_EMPTY_FIELD_LAT_LNG = -605;
    const ERROR_EMPTY_FIELD_KATEGORIA = -606;
    const ERROR_EMPTY_FIELD_IMAGE = -607;
    const ERROR_EMPTY_FIELD_MESTO = -608;
    const ERROR_NO_CITY_FOUND_BY_ID = -609;
    const ERROR_NO_CATEGORY_FOUND_BY_ID = -610;

    const ERROR_INVALID_LAT_OR_LONG = -611;

    const ERROR_OTHER = -999;

    const SUCCESSFUL_COMMENT = 601;
    const SUCCESSFUL_SUBMIT = 300;

    public function init()
    {
        parent::init();
        Yii::info(Yii::$app->request->getUrl(), 'legacy-api');
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        // Yii::info($result, 'legacy-api');

        return $result;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'appAuthenticator' => [
                'class' => PostParamAuth::className(),
                'tokenParam' => 'key',
                'user' => Yii::$app->appUser,
                'enabled' => false,
            ],
            'userAuthenticator' => [
                'class' => HttpBasicAuth::className(), // user
                'user' => Yii::$app->user,
                'enabled' => false,
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'pageCache' => [
                'class' => PageCache::className(),
                'duration' => 60 * 10,
                'enabled' => false,
                'variations' => [
                    Yii::$app->request->get(),
                ],
            ],
        ]);
    }

    public static function setResponseData($code, $customMessage = null)
    {
        $response = Yii::$app->response;
        if (array_key_exists($code, self::$successMap)) {
            $response->setStatusCode(200);
            $response->data = self::$successMap[$code];
            Yii::info("\n" . Json::encode($response->data, JSON_PRETTY_PRINT) . ' | ' . $customMessage, 'legacy-api');
            return;
        }
        if (array_key_exists($code, self::$errorMap)) {
            switch ($code) {
                case self::ERROR_LOGIN_REQUIRED:
                    $response->setStatusCode(401);
                    break;
                default:
                    $response->setStatusCode(400);
                    break;
            }
            $response->data = self::$errorMap[$code];
            Yii::info("\n" . Json::encode($response->data, JSON_PRETTY_PRINT) . ' | ' . $customMessage, 'legacy-api');
            return;
        }

        throw new BadRequestHttpException('Wrong response code: ' . $code . '!');
    }
}
