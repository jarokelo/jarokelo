<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\db\CronLog */

$this->title = '#' . $model->id . ' @ ' . Yii::$app->formatter->asDatetime($model->created_at);
$this->params['breadcrumbs'][] = ['label' => Yii::t('cron-log', 'Cron Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cron-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
                'attribute' => 'type',
                'value' => \yii\helpers\ArrayHelper::getValue($model->types(), $model->type),
            ],
            'output:ntext',
            'error_message:ntext',
            'runtime',
            'created_at:datetime',
            //'updated_at:datetime',
        ],
    ]) ?>

</div>
