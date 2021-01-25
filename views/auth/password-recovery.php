<?php

/* @var yii\web\View $this */
/* @var \app\models\forms\LoginForm $model */
/* @var bool $fromNewReport */
/* @var string $email */

use app\components\helpers\Link;
use app\components\ActiveForm;
use app\components\helpers\SVG;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'password-recovery-form',
    'action' => Link::to(Link::AUTH_PASSWORD_RECOVERY),
    'method' => 'post',
]) ?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-md-8 col-lg-5 col--off text-left">
            <section class="section section--grey section--rounded">
                <div class="form__container form__container--desktop">
                    <legend class="form__legend"><?= Yii::t('auth', 'password-recovery-legend') ?></legend>

                    <div class="form__row">
                        <p><?= Yii::t('auth', 'password-recovery-paragraph') ?></p>
                    </div>

                    <?= Html::errorSummary($model) ?>

                    <?= $form->field($model, 'email', ['template' => '{label}<div class="input-group">{input}<div class="input-group__addon">' . SVG::icon(SVG::ICON_CHECKMARK, ['class' => 'icon']) . '</div></div>{error}'])
                        ->textInput(['class' => 'input--light', 'validate' => true, 'valid' => 'email', 'value' => isset($email) ? $email : '']) ?>

                    <div class="form__row">
                        <?= Html::submitButton(Yii::t('button', 'i-would-like-new-password'), ['class' => 'button button--success button--large button--full']) ?>
                    </div>

                    <div class="form__row text-center">
                        <p><?= Yii::t('auth', 'remember-your-password-question')?></p>
                        <p>
                            <strong><?= Html::a(Yii::t('button', 'log-in'), Link::to(Link::AUTH_LOGIN), ['class' => 'link link--info']) ?></strong>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
