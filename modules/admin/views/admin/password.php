<?php

use app\models\forms\RegistrationForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\AdminForm $model */
/* @var string $title */

$this->title = $model->getFullName();
$this->params['breadcrumbs'] = [$this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['admin/index'], 'label' => Yii::t('menu', 'admin')];

?>

<div class="row">
    <?php $form = ActiveForm::begin([
        'id' => 'admin-password-update',
        'action' => ['admin/password', 'id' => $model->id],
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>

    <div>
        <?php if ($model->is_old_password):?>
            <div class="alert alert-danger">
                <?= Yii::t('profile', 'old_password_alert') ?>
            </div>
        <?php endif; ?>
        <div class="block--grey">
            <h3><?= Yii::t('admin', 'update.password_change') ?></h3>

            <?= $form->field($model, 'old_password')->passwordInput() ?>
            <?= $form->field($model, 'new_password')->passwordInput() ?>
            <?= $form->field($model, 'repeat_password')->passwordInput() ?>

            <p>
                <b><?php echo Yii::t('profile', 'your_password_must_be'); ?></b>
            </p>
            <ul class="profile_ul list--validate" validate="#<?= Html::getInputId($model, 'new_password') ?>">
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
            </ul>
            <br>

            <?= Html::submitButton(Yii::t('button', 'update'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
