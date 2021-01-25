<?php

use app\components\widgets\Pjax;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\db\search\CronLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('cron-log', 'Cron Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row block--grey">
        <?php $form = ActiveForm::begin([
            'id' => 'cron-log-grid-view-search',
            'enableClientValidation' => false,
            'action' => ['cron-log/index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'class' => 'change-pjax-submit',
                'data-pjax-selector' => '#cron-log-search',
            ],
        ]) ?>
        <div class="col-md-4">
            <?= $form->field($searchModel, 'start_date')->widget(\kartik\date\DatePicker::className(), [
                'pluginOptions' => ['format' => 'yyyy-mm-dd'],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($searchModel, 'end_date')->widget(\kartik\date\DatePicker::className(), [
                'pluginOptions' => ['format' => 'yyyy-mm-dd'],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($searchModel, 'has_error')->dropDownList([
                0 => Yii::t('yii', 'No'),
                1 => Yii::t('yii', 'Yes'),
            ]) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>

    <?php Pjax::begin([
        'id' => 'cron-log-search',
        'formSelector' => '#cron-log-grid-view-search',
        'options' => [
            'data-pjax-target' => 'cron-log-grid-view-search',
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{items}\n<div class=\"text-center\">{pager}</div>",
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a('#' . $model->id, ['view', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return \yii\helpers\ArrayHelper::getValue($model->types(), $model->type);
                },
            ],
            [
                'attribute' => 'output',
                'format' => 'ntext',
                'value' => function ($model) {
                    $value = $model->output ? \yii\helpers\StringHelper::truncate($model->output, 70, '...', 'UTF-8', true) : null;
                    return Html::encode($value);
                },
            ],
            [
                'attribute' => 'error_message',
                'format' => 'ntext',
                'value' => function ($model) {
                    $value = $model->output ? \yii\helpers\StringHelper::truncate($model->error_message, 70, '...', 'UTF-8', true) : null;
                    return Html::encode($value);
                },
            ],
            [
                'attribute' => 'runtime',
                'value' => function ($model) {
                    return round($model->runtime, 2) . ' ' . Yii::t('cron-log', 'seconds');
                },
            ],
            'created_at:datetime',
            // 'updated_at',
        ],
    ]);

    Pjax::end(); ?>
</div>
