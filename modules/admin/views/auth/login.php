<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \app\modules\admin\models\LoginForm $model
 */
$this->title = Yii::t('label', 'generic.login');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'form-login']]) ?>
        <div class="panel panel-login">
            <div class="panel-heading">
                <h2><?= Html::encode($this->title) ?></h2>
            </div>
            <div class="panel-body">
                <?= $form->field($model, 'email')->textInput(['autofocus' => 'autofocus', 'placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <div class="panel-footer">
                <?= Html::submitButton(Yii::t('button', 'login'), ['class' => 'btn btn-lg btn-login btn-block', 'name' => 'login-button']) ?>
            </div>
        </div> <!-- panel -->
        <?php ActiveForm::end() ?>
    </div> <!-- col -->
</div> <!-- row -->
