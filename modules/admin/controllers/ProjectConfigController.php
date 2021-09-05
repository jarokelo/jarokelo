<?php

namespace app\modules\admin\controllers;

use app\models\db\Admin;
use Yii;
use app\models\db\ProjectConfig;
use app\models\db\search\ProjectConfigSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 *
 */
class ProjectConfigController extends Controller
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
     * Lists all ProjectConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PROJECT_CONFIG_VIEW)) {
            return $this->goHome();
        }

        $searchModel = new ProjectConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProjectConfig model.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PROJECT_CONFIG_ADD)) {
            return $this->redirect(['index']);
        }

        $model = new ProjectConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectConfig model.
     *
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($key)
    {
        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PROJECT_CONFIG_EDIT)) {
            return $this->redirect(['index']);
        }

        $model = $this->findModel($key);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ProjectConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return ProjectConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($key)
    {
        if (($model = ProjectConfig::find()->where(['key' => $key])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('project_config', 'The requested page does not exist.'));
    }
}
