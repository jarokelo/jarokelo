<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\CustomQuestion */

$this->title = Yii::t('custom_form', 'Egyedi kérdés módosítása: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('custom_form', 'Egyedi kérdések'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('custom_form', 'Módosítás');
?>
<div class="custom-form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
