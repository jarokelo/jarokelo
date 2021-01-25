<?php

namespace app\modules\api\controllers;

use app\components\helpers\Key;
use app\models\db\User;
use app\modules\api\components\LoginAuth;
use Yii;
use yii\helpers\Url;
use app\models\db\Report;
use app\models\forms\LoginForm;
use app\modules\api\components\ApiController;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class LoginController extends ApiController
{
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
        $behaviors['userAuthenticator'] = [
            'class' => LoginAuth::className(),
        ];

        return $behaviors;
    }


    public function actionIndex()
    {
        $this->enableCsrfValidation = false;

        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->api_token = Key::generate(20);
        $user->save();

        return [
            'id' => $user->id,
            'api_token' => $user->api_token,
            'email' => $user->email,
            'fullname' => $user->getFullName(),
            'image' => Url::base(true) . $user->getPictureUrl(),
            'city' => [
                'id' => $user->city_id,
                'name' => ArrayHelper::getValue($user, 'city.name'),
            ],
            'district' => [
                'id' => $user->district_id,
                'name' => ArrayHelper::getValue($user, 'district.name'),
            ],
            'statistics' => [
                'leaderboard' => $user->getRank(),
                'reports' => $user->getReports()->asArray()->count(),
                'reports_solved' => Report::countUserResolved($user->id),
                'reports_unsolved' => Report::countUserUnresolved($user->id),
            ],
        ];
    }
}
