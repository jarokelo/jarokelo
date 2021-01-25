<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\Progress */

$this->title = Yii::t('admin', 'label.create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'progress.label'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="progress-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
