<?php

namespace app\controllers;

use app\components\helpers\SVG;
use app\models\commands\DailyMail;
use Yii;
use app\components\Header;
use yii\captcha\CaptchaAction;
use yii\web\Controller;

/**
 * Handles the rendering of the static pages.
 *
 * @package app\controllers
 */
class StaticController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => CaptchaAction::className(),
                'fixedVerifyCode' => null,
            ],
        ];
    }

    /**
     * Renders the API documentation
     *
     * @return string
     */
    public function actionApi()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.api'));
        return $this->render('/api/index');
    }

    public function actionDailyMail($date = null)
    {
        DailyMail::process($date);

        return $this->render('flash');
    }
}
