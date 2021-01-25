<?php
use app\components\ActiveForm;
use app\components\helpers\SVG;
use app\models\forms\NewPasswordForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
]); ?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-md-8 col-lg-5 col--off text-left">
            <section class="section section--grey section--rounded">
                <div class="form__container form__container--desktop">
                    <legend class="form__legend"><?= Yii::t('auth', 'set-new-password.legend') ?></legend>

                    <?= $form->field($newPasswordForm, 'new_password', ['template' => '{label}<div class="input-group input-group--addon">{input}<div class="input-group__addon input-group--pointer"><div toggle-password>' . SVG::icon(SVG::ICON_EYE, ['class' => 'icon']) . '</div></div></div>{error}'])
                        ->passwordInput(['class' => 'input--light']) ?>
                    <?= $form->field($newPasswordForm, 'repeat_password', ['template' => '{label}<div class="input-group input-group--addon">{input}<div class="input-group__addon input-group--pointer"><div toggle-password>' . SVG::icon(SVG::ICON_EYE, ['class' => 'icon']) . '</div></div></div>{error}'])
                        ->passwordInput(['class' => 'input--light']) ?>

                    <div class="form__row">
                        <p>
                            <strong>Jelszavad legyen:</strong>
                        </p>
                        <ul class="list--validate"
                            validate="#<?= Html::getInputId($newPasswordForm, 'new_password') ?>">
                            <li valid="<?= NewPasswordForm::PASSWORD_REGEX_LENGTH ?>">legalább 6 karakter hosszú,</li>
                            <li valid="<?= NewPasswordForm::PASSWORD_REGEX_NUMBER ?>">legyen benne szám,</li>
                            <li valid="<?= NewPasswordForm::PASSWORD_REGEX_CAPITAL ?>">legyen benne egy nagybetű.</li>
                        </ul>
                    </div>

                    <?= Html::submitButton(Yii::t('button', 'set-new-password'), ['class' => 'button button--success button--large button--full']); ?>

                </div>
            </section>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
