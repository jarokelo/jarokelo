<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\MapLayer */

$this->title = Yii::t('admin', 'map-layers.label_update', ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'map-layers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'label.update');
?>
<div class="kml-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
