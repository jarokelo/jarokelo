<?php

/* @var yii\web\View $this */
/* @var \app\models\forms\LoginForm $model */
/* @var bool $fromNewReport */

use app\components\helpers\Link;
use app\components\ActiveForm;
use app\components\helpers\SVG;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'action' => Link::to(Link::AUTH_LOGIN),
    'method' => 'post',
]) ?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-lg-5 col--off text-left">
            <section class="section section--grey section--rounded">
                <div class="form__container form__container--desktop">
                    <legend class="form__legend"><?= Yii::t('auth', 'login-legend') ?></legend>

                    <div class="row">
                        <div class="col-xs-12">
                            <?= $this->render('_social-login') ?>
                            <div class="legend"><?= Yii::t('auth', 'or') ?></div>
                        </div>
                    </div>

                    <?= $form->field($model, 'email', ['template' => '{label}<div class="input-group">{input}<div class="input-group__addon">' . SVG::icon(SVG::ICON_CHECKMARK, ['class' => 'icon']) . '</div></div>{error}'])
                        ->input('email', ['autofocus' => 'autofocus', 'class' => 'input--light', 'validate' => true, 'valid' => 'email']) ?>
                    <?= $form->field($model, 'password', ['template' => '{label}<div class="input-group input-group--addon">{input}<div class="input-group__addon input-group--pointer"><div toggle-password>' . SVG::icon(SVG::ICON_EYE, ['class' => 'icon']) . '</div></div></div>{error}'])
                        ->passwordInput(['class' => 'input--light']) ?>

                    <br>

                    <div class="form__row">
                        <div class="row middle-xs">
                            <div class="col-xs-6 form__row--nomargin">
                                <?= $form->field($model, 'rememberMe')->checkbox(['checkbox-label' => 'Emlékezz rám', 'checkbox-css' => 'checkbox--light rememberMe-checkbox']) ?>
                            </div>
                            <div class="col-xs-6 text-right mb-15">
                                <strong>
                                    <?= Html::a(Yii::t('auth', 'forgot-password-question'), Link::to(Link::AUTH_PASSWORD_RECOVERY), ['class' => 'link link--info']) ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="form__row">
                        <?= Html::submitButton(Yii::t('button', 'login'), ['class' => 'button button--success button--large button--full']) ?>
                    </div>

                    <?php if ($fromNewReport == 1) { ?>
                        <div class="form__row">
                            <p class="text-center">
                                <strong><?= Html::a(Yii::t('button', 'anonymous_report'), Link::to(Link::ANONYMOUS_REPORT), ['class' => 'button--link']) ?></strong>
                            </p>
                        </div>
                    <?php } ?>

                    <div class="form__row text-center">
                        <p><?= Yii::t('auth', 'no-accout-yet?') ?></p>
                        <p>
                            <strong><?= Html::a(Yii::t('button', 'register!'), Link::to(Link::AUTH_REGISTER), ['class' => 'link link--info']) ?></strong>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php ActiveForm::end();

$this->registerJs('$(document).ready(function() {
    function rememberMeHandler() {
        $.post("/auth/remember-me-handler");
    }
    
    $(".rememberMe-checkbox").click(function () {
        rememberMeHandler();
    });
    
    $("[for=loginform-rememberme]").click(function () {
        rememberMeHandler();
    });
});');
