<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\LoginForm;

use Yii;

use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Handles authentication related actions.
 *
 * @package app\modules\admin\controllers
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'login';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Handles the login of an Admin.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->login()) {
            if ($model->getUser()->is_old_password) {
                return $this->redirect(['/admin/admin/password']);
            }
            return $this->goBack();
        }

        $this->layout = 'login';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Handles the logout of an Admin.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->identity->logout();

        return $this->redirect(['auth/login']);
    }
}
