<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\ReportTaxonomy */

$this->title = Yii::t('report_taxonomy', 'Bejelentés alkategória módosítása: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('report_taxonomy', 'Bejelentés alkategóriák'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('report_taxonomy', 'Módosítás');
?>
<div class="report-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
