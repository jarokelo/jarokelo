<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\ReportCategory */

$this->title = Yii::t('app', 'Bejelentés kategória módosítása: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bejelentés kategóriák'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Módosítás');
?>
<div class="report-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
