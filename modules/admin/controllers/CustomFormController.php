<?php

namespace app\modules\admin\controllers;

use app\components\ActiveForm;
use app\models\db\Admin;
use Yii;
use app\models\db\CustomForm;
use app\models\db\search\CustomFormSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CustomFormController implements the CRUD actions for CustomForm model.
 */
class CustomFormController extends Controller
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
     * Lists all CustomForm models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CUSTOM_FORM_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new CustomFormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     *
     */
    public function successMsg()
    {
        Yii::$app->session->setFlash(
            'success',
            Yii::t('custom_form', 'Egyedi adatlap sikeresen mentve')
        );
    }

    /**
     * @param CustomForm $model
     */
    public function errorMsg(CustomForm $model)
    {
        $errors = [];

        if (!empty($model->getErrors('custom_questions'))) {
            $errors = $model->getErrors('custom_questions');
        }

        Yii::$app->session->setFlash(
            'error',
            Yii::t('custom_form', 'Sikertelen mentés. ' . implode(',', $errors))
        );
    }

    /**
     * Creates a new CustomForm model.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CUSTOM_FORM_ADD)) {
            return $this->redirect(['index']);
        }

        $model = new CustomForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->successMsg();
                return $this->redirect(['index']);
            } else {
                $this->errorMsg($model);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomForm model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CUSTOM_FORM_EDIT)) {
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $validation = ActiveForm::validate($model);
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $validation;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->successMsg();
                return $this->redirect(['index']);
            } else {
                $this->errorMsg($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the CustomForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('custom_form', 'The requested page does not exist.'));
    }
}
