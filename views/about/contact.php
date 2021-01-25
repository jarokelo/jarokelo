<?php
use app\assets\AppAsset;
use app\components\ActiveForm;
use yii\helpers\Html;
use \himiklab\yii2\recaptcha\ReCaptcha;
use app\components\helpers\Link;
use yii\helpers\Url;

$bundle = AppAsset::register($this);
?>
<aside class="hero hero--contact hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-12 col-md-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('about', 'contact.title'); ?></h2>
                    <p class="hero__lead hero__lead--padding">
                        <?= Yii::t('about', 'contact.content'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
<div class="container contact">
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <h2 class="contact__title"><?= Yii::t('label', 'footer.write_to_us'); ?></h2>

            <?php
            $form = ActiveForm::begin([
                'id' => 'contact-submit-form',
                'enableClientValidation' => true,
                'options' => [
                    'class' => 'form',
                ],
            ]);
            ?>
            <?= $form->field($model, 'name')->textInput(); ?>
            <?= $form->field($model, 'email')->textInput(); ?>
            <?= $form->field($model, 'message')->textarea(); ?>
            <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha::className(), ['siteKey' => Yii::$app->params['reCaptcha']['siteKey']]); ?>

            <?= Html::submitButton(Yii::t('button', 'submit_contact'), ['class' => 'button button--large button--mobilegreen', 'name' => 'save']) ?>
            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-xs-12 col-md-4">
            <h2 class="contact__title"><?= Yii::t('label', 'footer.follow_us'); ?></h2>
            <?= $this->render('@app/views/layouts/_social-links') ?>
        </div>
    </div>
</div>
<aside class="hero hero--bottom">
    <div class="container">
        <h2 class="heading heading--1 hero__title"><?= Yii::t('app', 'hero.title'); ?></h2>
        <p class="hero__lead col-8 col--centered">
            <?= Yii::t('app', 'hero.lead'); ?>
        </p>

        <div class="hero__button">
            <?= Html::a(Yii::t('app', 'hero.report_problem'), Link::to(Link::CREATE_REPORT), ['class' => 'button button--large button--primary']); ?>
        </div>
    </div>
</aside>
