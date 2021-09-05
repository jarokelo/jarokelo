<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\CustomForm */

$this->title = Yii::t('custom_form', 'Egyedi adatlap létrehozása');
$this->params['breadcrumbs'][] = ['label' => Yii::t('custom_form', 'Egyedi adatlapok'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="custom-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
