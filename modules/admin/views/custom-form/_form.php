<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\db\CustomQuestion;

/* @var $this yii\web\View */
/* @var $model app\models\db\CustomForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="custom-form-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= \app\modules\admin\components\widgets\RelationHandlerWidget::widget(
        [
            'formName' => 'CustomForm[custom_questions][]',
            'existingRelations' => $model->getCustomQuestions(true),
            'selection' => CustomQuestion::getList(),
            'urlName' => 'custom-question',
            'label' => Yii::t('data', 'Kapcsolat létrehozása egyedi kérdésekkel'),
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('custom_form', 'Mentés'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
