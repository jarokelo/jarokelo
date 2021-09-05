<?php

namespace app\modules\admin\controllers;

use app\components\ActiveForm;
use app\models\db\Admin;
use app\models\db\search\ReportTaxonomySearch;
use Yii;
use app\models\db\ReportTaxonomy;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *
 */
class ReportTaxonomyController extends Controller
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
     * Lists all ReportCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_TAXONOMY_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new ReportTaxonomySearch();
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
            Yii::t('report_category', 'Kategória sikeresen mentve')
        );
    }

    /**
     * Creates a new ReportCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_TAXONOMY_ADD)) {
            return $this->redirect(['index']);
        }

        $model = new ReportTaxonomy();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->successMsg();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ReportCategory model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_TAXONOMY_EDIT)) {
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->successMsg();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ReportCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReportTaxonomy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportTaxonomy::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('report_taxonomy', 'The requested page does not exist.'));
    }
}
