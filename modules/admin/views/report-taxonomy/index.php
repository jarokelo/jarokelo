<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\ReportTaxonomy;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\ReportTaxonomySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('report_taxonomy', 'Bejelentés alkategóriák');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('report_taxonomy', 'Bejelentés alkategória létrehozása'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'value' => function (ReportTaxonomy $model) {
                    return $model->is_active == $model::STATUS_INACTIVE
                        ? Yii::t('data', 'inactive')
                        : Yii::t('data', 'active');
                },
                'filter' => [
                    ReportTaxonomy::STATUS_INACTIVE => Yii::t('data', 'inactive'),
                    ReportTaxonomy::STATUS_ACTIVE => Yii::t('data', 'active'),
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
