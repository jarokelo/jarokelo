<?php

/* @var yii\web\View $this */
/* @var RegistrationForm $model */

use app\components\ActiveForm;
use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\forms\RegistrationForm;
use yii\bootstrap\Html;
use app\assets\ButtonAsset;

ButtonAsset::register($this);
?>

<?php $form = ActiveForm::begin([
    'id' => 'registration-form',
    'action' => Link::to(Link::AUTH_REGISTER),
    'method' => 'post',
    'enableAjaxValidation' => true,
]) ?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-lg-5 col--off text-left">
            <section class="section section--grey section--rounded">
                <div class="form__container form__container--desktop">
                    <legend class="form__legend"><?= Yii::t('auth', 'register-legend') ?></legend>

                    <div class="row">
                        <div class="col-xs-12">
                            <?= $this->render('_social-login') ?>
                            <div class="legend"><?= Yii::t('auth', 'or') ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <?= $form->field($model, 'last_name')->textInput(['class' => 'input--light']) ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $form->field($model, 'first_name')->textInput(['class' => 'input--light']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'email', [
                        'template' => '{label}<div class="input-group">{input}<div class="input-group__addon">' . SVG::icon(SVG::ICON_CHECKMARK, ['class' => 'icon']) . '</div></div>{error}',
                        'errorOptions' => ['class' => 'help-block', 'encode' => false],
                    ])->input('email', ['class' => 'input--light', 'validate' => true, 'valid' => 'email']) ?>

                    <?= $form->field($model, 'password', ['template' => '{label}<div class="input-group input-group--addon">{input}<div class="input-group__addon input-group--pointer"><div toggle-password>' . SVG::icon(SVG::ICON_EYE, ['class' => 'icon']) . '</div></div></div>{error}'])
                        ->passwordInput(['class' => 'input--light']) ?>
                    <?= $form->field($model, 'password_repeat', ['template' => '{label}<div class="input-group input-group--addon">{input}<div class="input-group__addon input-group--pointer"><div toggle-password>' . SVG::icon(SVG::ICON_EYE, ['class' => 'icon']) . '</div></div></div>{error}'])
                        ->passwordInput(['class' => 'input--light']) ?>

                    <div class="form__row">
                        <p>
                            <strong><?= Yii::t('auth', 'password-requirements-label') ?></strong>
                        </p>
                        <ul class="list--validate" validate="#<?= Html::getInputId($model, 'password') ?>">
                            <li valid="<?= RegistrationForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
                            <li valid="<?= RegistrationForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
                            <li valid="<?= RegistrationForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
                        </ul>
                    </div>

                    <div class="form__row">
                            <?= $form->field(
                                $model,
                                'privacy_policy',
                                [
                                    'template' => '<label for="registrationform-privacy_policy" class="checkbox--label checkbox--wrap">{input}<div class="top checkbox--wrap"><div>'
                                        . Yii::t('auth', 'register-consent-tick-box-text') . '</div><div class="profile__hint">'
                                        . Yii::t('auth', 'register-consent-tick-box-help-text', ['link' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TOS])]) . '</div></div></label><br><br>{error}',
                                ]
                            )
                                ->checkbox(['checkbox-css' => 'top checkbox--left']);
                            ?>
                    </div>

                    <div class="form__row">
                        <?= Html::submitButton(Yii::t('button', 'register'), ['class' => 'button button--success button--large button--full disable-after-submit']) ?>
                    </div>

                    <div class="form__row text-center">
                        <p><?= Yii::t('auth', 'already-registered?') ?></p>
                        <p>
                            <strong><?= Html::a(Yii::t('button', 'log-in'), Link::to(Link::AUTH_LOGIN), ['class' => 'link link--info']) ?></strong>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php ActiveForm::end();

$this->registerJs(
    '$(document).ready(function() {
         Button.disableAfterSubmit("#registration-form");
     });'
);
