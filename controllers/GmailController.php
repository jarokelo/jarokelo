<?php

namespace app\controllers;

use app\components\helpers\GmailApi;
use app\models\db\GmailEmailToken;
use yii\web\Controller;
use yii\web\Response;
use Yii;

/**
 * Handles Gmail OAuth tokens
 */
class GmailController extends Controller
{
    /**
     * Show data.
     *
     * @return string
     */
    public function actionStore()
    {
        return $this->render('store');
    }

    /**
     * Store token information
     *
     * @return array|string
     */
    public function actionStoreToken()
    {
        $type = Yii::$app->request->post('type', '');

        if (empty($type)) {
            return $this->render('store');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];

        $api = new GmailApi();

        switch ($type) {
            case 'get-auth-url':
                $response['auth-url'] = $api->getAuthUrl();
                break;
            case 'store':
                try {
                    $code = Yii::$app->request->post('code');
                    $accessToken = $api->fetchAccessToken($code);
                    $api = new GmailApi();
                    $client = $api->getClient();
                    $client->setAccessToken($accessToken);
                    $profile = $api->getUserProfile();
                    $accessToken['email'] = $profile['emailAddress'];
                    GmailEmailToken::saveByToken($accessToken);
                    $response['email'] = $accessToken['email'];
                } catch (\Exception $e) {
                    $response['error'] = $e->getMessage();
                }

                break;
        }

        return $response;
    }
}
