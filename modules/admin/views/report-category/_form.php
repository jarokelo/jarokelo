<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\db\CustomForm;
use app\models\db\ReportTaxonomy;

/* @var $this yii\web\View */
/* @var $model app\models\db\ReportCategory */
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
            'formName' => 'ReportCategory[taxonomyRelationList][]',
            'existingRelations' => $model->getTaxonomyRelationList(),
            'selection' => ReportTaxonomy::getList(),
            'urlName' => 'report-taxonomy',
            'label' => Yii::t('data', 'Kapcsolat létrehozása alkategóriákkal'),
        ]
    ) ?>

    <?= \app\modules\admin\components\widgets\RelationHandlerWidget::widget(
        [
            'formName' => 'ReportCategory[formRelationList][]',
            'existingRelations' => $model->getFormRelationList(),
            'selection' => CustomForm::getList(),
            'urlName' => 'custom-form',
            'label' => Yii::t('report-category', 'Egyedi űrlap csatolása'),
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('data', 'save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
