<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\Progress */

$this->title = Yii::t('admin', 'progress.label_update', ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'progress.label'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'label.update');
?>
<div class="progress-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
