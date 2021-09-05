<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\ProjectConfig;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\ProjectConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('project_config', 'Projekt konfigurációk');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('project_config', 'Projekt konfiguráció létrehozása'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'key',
                'filter' => $filterKeys = ProjectConfig::getFilterKeys(),
                'value' => function (ProjectConfig $model) use ($filterKeys) {
                    if (isset($filterKeys[$model->key])) {
                        return $filterKeys[$model->key];
                    }

                    return $model->key;
                },
            ],
            [
                'attribute' => 'value',
                'filter' => $filterValues = ProjectConfig::getFilterValues(),
                'value' => function (ProjectConfig $model) use ($filterValues) {
                    if (isset($filterValues[$model->value])) {
                        return $filterValues[$model->value];
                    }

                    return $model->value;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, ProjectConfig $model) {
                        return '<a href="/admin/project-config/update?key=' . $model->key . '" title="Szerkesztés" aria-label="Szerkesztés" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
