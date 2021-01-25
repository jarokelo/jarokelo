<?php

namespace app\modules\api\components;

use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use \yii\web\Response;

class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // separate auth method by request because they conflict
        if (Yii::$app->request->isPost) {
            // currently just post requests auth via postParamAuth
            $authMethod = [
                'userAuthenticator' => [
                    'class' => PostParamAuth::className(),
                    'tokenParam' => 'api_token',
                    'enabled' => true,
                ],
            ];
        } else {
            // query authentication is default
            $authMethod = [
                'appAuthenticator' => [
                    'class' => QueryParamAuth::className(),
                    'tokenParam' => 'token',
                    'user' => Yii::$app->appUser,
                ],
            ];
        }

        return array_merge(
            parent::behaviors(),
            [
                'contentNegotiator' => [
                    'class' => ContentNegotiator::className(),
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                    ],
                ],
            ],
            $authMethod
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            Yii::$app->response->on('beforeSend', function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                }
            });

            return true;
        }

        return false;
    }
}
