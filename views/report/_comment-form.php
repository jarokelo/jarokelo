<?php
use app\components\ActiveForm;
use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\User;
use app\models\forms\CommentForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$isGuest = Yii::$app->user->isGuest;

$this->registerJs("$('#template-source').data('close-template', " . Json::encode((string)SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon'])) . ');');

?>
<div id="template-source"></div>
<div class="form report__form">
    <?php $commentForm = new CommentForm(); ?>
    <?php $form = ActiveForm::begin([
        //'id' => 'report-comment-form',
        'enableAjaxValidation' => false,
        'options' => [
            'data-pjax' => '0',
            'class' => 'init-loader',
            'enctype' => 'multipart/form-data',
        ],
    ]) ?>
    <?php if ($isGuest): ?>
        <div class="comment__login">
            <p><?= Yii::t('report', 'comment.login.required') ?></p>
            <?= Html::a(Yii::t('button', 'login'), Link::to(Link::AUTH_LOGIN), ['class' => 'button button--small button--success']); ?>
            <p>
                 <?= Yii::t('auth', 'no-accout-yet?') . ' ' . Html::a(Yii::t('button', 'register!'), Link::to(Link::AUTH_REGISTER), ['class' => 'link link--info']) ?>
            </p>
        </div>
    <?php else: ?>
        <div class="comment comment--default comment--form <?= $isGuest ? 'hidden--mobile' : '' ?>">
            <img src="<?= User::getPictureUrl(Yii::$app->user->id) ?>" alt="" class="comment__media">
            <div class="comment__body">
                <?= $form->field($commentForm, 'comment')->textarea([
                    'placeholder' => Yii::t('label', 'comment.placeholder'),
                    'class' => 'field field--full-width autogrow-textarea',
                ])->label(false) ?>

                <div class="file-upload file-upload--previews dropzone"></div>
            </div>
            <div class="report__form__buttons">
                <div class="file-upload file-upload--comment dropzone <?= ($isGuest ? 'file-upload--disabled' : null) ?>" data-upload-url="<?= ($isGuest ? '' : Url::to(['/report/dropzone.comment'])) ?>"
                     data-delete-url="<?= ($isGuest ? '' : Url::to(['/report/dropzone.remove'])) ?>"
                     data-input-name="<?= Html::getInputName($commentForm, 'pictures') ?>">

                    <div class="dz-message">
                        <?= SVG::icon(SVG::ICON_CAMERA, ['class' => 'file-upload__icon filter__icon']) ?>
                    </div>
                </div>

                <?= Html::submitButton(Yii::t('button', 'send2'), ['class' => 'button button--large button--success', 'disabled' => ($isGuest ? 'disabled' : false)]) ?>
            </div>
        </div>
    <?php endif; ?>
    <?php ActiveForm::end() ?>
</div>
