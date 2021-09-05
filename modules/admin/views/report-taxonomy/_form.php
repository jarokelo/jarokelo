<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\db\CustomForm;

/* @var $this yii\web\View */
/* @var $model app\models\db\ReportTaxonomy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-category-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->dropDownList([
        $model::STATUS_INACTIVE => Yii::t('data', 'inactive'),
        $model::STATUS_ACTIVE => Yii::t('data', 'active'),
    ]) ?>

    <?= \app\modules\admin\components\widgets\RelationHandlerWidget::widget(
        [
            'formName' => 'ReportTaxonomy[formRelationList][]',
            'existingRelations' => $model->getFormRelationList(),
            'selection' => CustomForm::getList(),
            'urlName' => 'custom-form',
            'label' => Yii::t('report-taxonomy', 'Egyedi űrlap csatolása'),
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('report_taxonomy', 'Mentés'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
