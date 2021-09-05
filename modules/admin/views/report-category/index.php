<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\ReportCategory;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\ReportCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bejelentés kategóriák');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Bejelentés kategória létrehozása'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute' => 'is_active',
                'value' => function (ReportCategory $model) {
                    return $model->is_active == $model::STATUS_ACTIVE
                        ? Yii::t('data', 'active')
                        : Yii::t('data', 'inactive');
                },
                'filter' => [
                    ReportCategory::STATUS_INACTIVE => Yii::t('data', 'inactive'),
                    ReportCategory::STATUS_ACTIVE => Yii::t('data', 'active'),
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
