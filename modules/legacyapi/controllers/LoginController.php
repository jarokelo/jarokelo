<?php

namespace app\modules\legacyapi\controllers;

use app\components\helpers\Key;
use app\models\db\User;
use Yii;
use yii\helpers\Url;
use app\models\db\Report;
use app\models\forms\LoginForm;
use app\modules\legacyapi\components\ApiController;
use yii\web\HttpException;

class LoginController extends ApiController
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['userAuthenticator']['enabled'] = true;

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $this->enableCsrfValidation = false;

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($user === null) {
            static::setResponseData(self::ERROR_LOGIN_REQUIRED);
        }

        return [
            'code' => 100,
            'message' => 'Logged in as ' . $user->email,
            'user_id' => $user->id,
            'user_name' => $user->first_name,
            'user_surname' => $user->last_name,
            'user_username' => $user->email,
            'user_anonymous' => 0, /* anonymous user (this is stored in the report now, not in the user, so we give back a 0 always) */
            'user_statistics' => [
                'vyriesene' => Report::countUserResolved($user->id), /* solved */
                'neriesene' => Report::countUserUnresolved($user->id), /* unsolved */
                'zaslane' => Report::countUserInProgress($user->id), /* sent */
                'rozhodnite' => Report::countUserWaitingForDecision($user->id), /* decide */
                'spolu' => Report::countUserReports($user->id), /* all */
            ],
        ];
    }
}
