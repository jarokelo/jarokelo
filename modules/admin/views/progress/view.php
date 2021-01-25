<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\db\Admin;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\db\Progress */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'progress.label'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="progress-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'label.update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'label.delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'label.confirm_delete'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'amount',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => call_user_func(function () use ($model) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_EDIT)) {
                        return $model->createdBy->fullName;
                    } else {
                        return Html::a(
                            $model->createdBy->fullName,
                            Url::to(['user/update', 'id' => $model->created_by]),
                            ['data-pjax' => 0]
                        );
                    }
                }),
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => call_user_func(function () use ($model) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_EDIT)) {
                        return $model->updatedBy->fullName;
                    } else {
                        return Html::a(
                            $model->updatedBy->fullName,
                            Url::to(['user/update', 'id' => $model->updated_by]),
                            ['data-pjax' => 0]
                        );
                    }
                }),
            ],

            'created_at:date',
            'updated_at:date',
        ],
    ]) ?>

</div>
