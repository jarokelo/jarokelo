<?php

use app\models\db\Institution;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\assets\ButtonAsset;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\SendForm $model */

ButtonAsset::register($this);
?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'report-send',
        'action' => ['report/send', 'id' => $model->report->id],
        'enableClientValidation' => true,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
                aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><?= Yii::t('report', 'send') ?></h4>
    </div>

    <div class="modal-body real-send">
        <?= $form->field($model, 'institution_id')->widget(Select2::className(), [
            'data' => Institution::getInstitutions($model->report->city_id, false, true),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'id' => 'report-send-select2',
                'class' => 'load-institution-contacts',
                'placeholder' => Yii::t('report', 'send.institution.placeholder'),
                'data-url' => Url::to(['institution/contact-list', 'id' => 'ph', 'rid' => $model->report->rule_id]),
                'data-target' => '.contact-container',
            ],
        ]) ?>
        <?php $hideContacts = !$model->loadInstitution() ?>
        <div class="<?= $hideContacts ? 'hidden' : '' ?> can-send-email">
            <?php $noEmail = \app\models\db\City::findOne(['id' => $model->report->city_id, 'email_address' => '']) ?>
            <div class="can-send-email-container"><?= $hideContacts ? '' : ($noEmail ? '<div class="alert alert-danger">' . Yii::t('report', 'send.email_no_sender', ['url' => \app\components\helpers\Link::to(['admin/city/' . $model->report->city_id])]) . '</div>' : '') ?></div>
        </div>
        <br>
        <div class="<?= $hideContacts ? 'hidden' : '' ?> institution-note"><h5><?= Yii::t('report', 'send.note') ?></h5>
            <div class="institution-note-container"><?= $hideContacts ? '' : $model->institution->note ?></div>
        </div>
        <br>
        <div class="<?= $hideContacts ? 'hidden' : '' ?> show-on-contact-load"><h5><?= Yii::t('report', 'send.email_addresses') ?></h5></div>
        <div class="<?= $hideContacts ? 'hidden' : '' ?> show-on-contact-load contact-container">
            <?php if (!$hideContacts): ?>
                <?= $this->render('@adminViews/institution/_contact_list', [
                    'contacts' => $model->institution->contacts,
                    'selectedContacts' => $model->selectedContacts,
                    'selectIfEmpty' => true,
                ]) ?>
            <?php endif ?>
        </div>
        <div class="extra-contacts"></div>
        <div>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' .
                Yii::t('report', 'send.add_contact'),
                '#',
                [
                    'class' => 'btn-modal-content btn btn-default',
                    'id' => 'send-form-add-contact',
                    'data-url' => Url::to(['report/send-field']),
                ]
            ) ?>
        </div>
        <br>
        <div><span class="fs-medium"><?= Yii::t('report', 'send.comment') ?></span></div>
    </div>

    <div class="modal-body hidden test-send">
        <?= $form->field($model, 'test')->hiddenInput()->label(false) ?>

        <h5><?= Yii::t('report', 'send.test_emails') ?></h5>
        <div class="extra-contacts">
            <?= $this->render('_send_field', [
                'test' => 1,
            ]) ?>
        </div>
        <div>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' .
                Yii::t('report', 'send.add_contact'),
                '#',
                [
                    'class' => 'btn-modal-content btn btn-default',
                    'id' => 'send-form-add-contact',
                    'data-url' => Url::to(['report/send-field', 'test' => 1]),
                ]
            ) ?>
        </div>
    </div>

    <div class="modal-footer">
        <div class="real-send">
            <?= Html::a(
                Yii::t('report', 'send.test_email'),
                '#',
                ['id' => 'toggle-test-send', 'class' => 'pull-left']
            ) ?>
        </div>
        <div class="test-send hidden">
            <?= Html::a(
                Yii::t('report', 'send.real_email'),
                '#',
                ['id' => 'toggle-test-send', 'class' => 'pull-left']
            ) ?>
        </div>
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
        &nbsp;
        <?= Html::submitButton(Yii::t('button', 'send'), ['class' => 'btn disable-after-submit btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(
    '$(document).ready(function() {
          Button.disableAfterSubmit("#report-send");
     });'
);
?>
