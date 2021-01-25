<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\MapLayer */

$this->title = Yii::t('admin', 'map-layers.create_update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'map-layers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="kml-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
