<?php

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use Yii;
use app\models\db\CustomQuestion;
use app\models\db\search\CustomQuestionSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *
 */
class CustomQuestionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CustomQuestion models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CUSTOM_QUESTION_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new CustomQuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new CustomQuestion model.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CUSTOM_QUESTION_ADD)) {
            return $this->redirect(['index']);
        }

        $model = new CustomQuestion();

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->successMsg();
                return true;
            } else {
                return $model->getErrors();
            }
        }

        if (is_array($model->answer_options)) {
            $model->answer_options = isset($model->oldAttributes['answers'])
                ? $model->oldAttributes['answers']
                : '[]';
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     *
     */
    public function successMsg()
    {
        Yii::$app->session->setFlash(
            'success',
            Yii::t('custom_form', 'Egyedi kérdés sikeresen mentve')
        );
    }

    /**
     * Updates an existing CustomQuestion model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->successMsg();
                return true;
            } else {
                return $model->getErrors();
            }
        }

        if (is_array($model->answer_options)) {
            $model->answer_options = isset($model->oldAttributes['answer_options'])
                ? $model->oldAttributes['answer_options']
                : '[]';
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the CustomQuestion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomQuestion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomQuestion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('custom_form', 'The requested page does not exist.'));
    }
}
