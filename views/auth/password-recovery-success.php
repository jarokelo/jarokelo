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
    'id' => 'password-recovery-form',
    'action' => Link::to(Link::AUTH_PASSWORD_RECOVERY),
    'method' => 'post',
]) ?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-lg-5 col--off text-center">
            <section class="section section--grey section--rounded">
                <div class="form__container form__container--desktop">
                    <div class="icon--seal">
                        <?= SVG::icon(SVG::ICON_EMAIL, ['class' => 'icon']) ?>
                    </div>

                    <legend class="form__legend"><?= Yii::t('auth', 'password-recovery-success-legend') ?></legend>

                    <div class="form__row">
                        <p><?= Yii::t('auth', 'password-recovery-success-paragraph') ?></p>
                    </div>

                    <div class="form__row text-center">
                        <p>
                            <strong><?= Html::a(Yii::t('button', 'back-to-home'), ['/'], ['class' => 'link link--info']) ?></strong>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
