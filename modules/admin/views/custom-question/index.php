<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\db\CustomQuestion;
use app\models\db\Admin;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\CustomQuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('custom_form', 'Egyedi kérdések');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="custom-form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('custom_form', 'Egyedi kérdés hozzáadása'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'question',
            [
                'attribute' => 'status',
                'filter' => $statusFilter = CustomQuestion::getStatusSelection(),
                'value' => function (CustomQuestion $model) use ($statusFilter) {
                    if (isset($statusFilter[$model->status])) {
                        return $statusFilter[$model->status];
                    }

                    return $model->status;
                },
            ],
            [
                'attribute' => 'type',
                'filter' => $typeFilter = CustomQuestion::getQuestionTypes(),
                'value' => function (CustomQuestion $model) use ($typeFilter) {
                    if (isset($typeFilter[$model->type])) {
                        return $typeFilter[$model->type];
                    }

                    return $model->type;
                },
            ],
            [
                'attribute' => 'required',
                'filter' => $requiredFilter = CustomQuestion::getRequiredSelection(),
                'value' => function (CustomQuestion $model) use ($requiredFilter) {
                    if (isset($requiredFilter[$model->required])) {
                        return $requiredFilter[$model->required];
                    }

                    return $model->required;
                },
            ],
            'created_at',
            'updated_at',
            [
                'attribute' => 'created_by',
                'filter' => $adminList = Admin::getAdminList(),
                'value' => function (CustomQuestion $model) use ($adminList) {
                    if (isset($adminList[$model->created_by])) {
                        return $adminList[$model->created_by];
                    }

                    return $model->created_by;
                },
            ],
            [
                'attribute' => 'updated_by',
                'filter' => $adminList = Admin::getAdminList(),
                'value' => function (CustomQuestion $model) use ($adminList) {
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
