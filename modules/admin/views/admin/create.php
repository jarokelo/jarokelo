<?php

use app\models\forms\RegistrationForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\modules\admin\models\AdminForm $model */

$this->title = Yii::t('label', 'new_admin');

$this->registerJS('
site.Validate.init()');
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'admin-create-ajax',
        'action' => ['admin/create'],
        'enableClientValidation' => true,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('admin', 'create') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'last_name')->textInput() ?>
        <?= $form->field($model, 'first_name')->textInput() ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'phone_number')->textInput() ?>

        <p>
            <b><?php echo Yii::t('profile', 'password_must_be'); ?></b>
        </p>
        <ul class="list--validate" validate="#<?= Html::getInputId($model, 'password') ?>">
            <li valid="<?= RegistrationForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
            <li valid="<?= RegistrationForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
            <li valid="<?= RegistrationForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
        </ul>
        <br />
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'add'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
