<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\CustomForm;
use app\models\db\Admin;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\CustomFormSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('custom_form', 'Egyedi adatlapok');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="custom-form-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('custom_form', 'Egyedi adatlap létrehozása'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'created_at',
            'updated_at',
            [
                'attribute' => 'status',
                'filter' => $statusFilter = CustomForm::getStatusSelection(),
                'value' => function (CustomForm $model) use ($statusFilter) {
                    if (isset($statusFilter[$model->status])) {
                        return $statusFilter[$model->status];
                    }

                    return $model->status;
                },
            ],
            [
                'attribute' => 'created_by',
                'filter' => $adminList = Admin::getAdminList(),
                'value' => function (CustomForm $model) use ($adminList) {
                    if (isset($adminList[$model->created_by])) {
                        return $adminList[$model->created_by];
                    }

                    return $model->created_by;
                },
            ],
            [
                'attribute' => 'updated_by',
                'filter' => $adminList = Admin::getAdminList(),
                'value' => function (CustomForm $model) use ($adminList) {
                    if (isset($adminList[$model->updated_by])) {
                        return $adminList[$model->updated_by];
                    }

                    return $model->updated_by;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
