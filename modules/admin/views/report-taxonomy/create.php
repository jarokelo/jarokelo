<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\ReportTaxonomy */

$this->title = Yii::t('report_taxonomy', 'Bejelentés alkategória létrehozása');
$this->params['breadcrumbs'][] = ['label' => Yii::t('report_taxonomy', 'Bejelentés alkategóriák'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
