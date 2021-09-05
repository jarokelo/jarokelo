<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\db\ProjectConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->dropDownList($model::getFilterKeys()) ?>

    <?= $form->field($model, 'value')->dropDownList($model::getFilterValues()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('project_config', 'Mentés'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
