<?php

use kartik\select2\Select2;
use app\components\jqueryupload\UploadWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\AnswerForm $model */
?>

<div class="modal-content">
    <?php
    $form = ActiveForm::begin([
        'id' => 'upload-answer-ajax',
        'action' => ['report/answer', 'id' => $model->report->id],
        'enableClientValidation' => true,
    ]);
    ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><?= Yii::t('report', 'answer.upload') ?></h4>
    </div>

    <div class="modal-body">
        <div id="step1" class="">
            <?= $form->field($model, 'institutionId')->widget(Select2::className(), [
                'data' => ArrayHelper::map($model->getInstitutions(), 'id', 'name'),
                'theme' => Select2::THEME_KRAJEE,
                'options' => [
                    'id' => 'answer-institution-select2',
                    'class' => 'load-institution-contacts',
                    'data-url' => Url::to(['institution/contact-list', 'id' => 'ph', 'rid' => $model->report->rule_id, 'radioList' => true]),
                    'data-target' => '.contact-container',
                ],
            ]) ?>
            <?php $hideContacts = $model->institution === null ?>
            <div><?= Yii::t('report', 'answer.email_addresses') ?></div>
            <div class="<?= $hideContacts ? 'hidden' : '' ?> show-on-contact-load contact-container">
                <?php if (!$hideContacts): ?>
                    <?= $form->field($model, 'contactId')->radioList(ArrayHelper::map($model->institution->contacts, 'id', function ($contact) {
                        /* @var \app\models\db\Contact $contact */
                        return $contact->name . ' (' . $contact->email . ')';
                    }))->label(false) ?>
                <?php endif ?>
            </div>
        </div>
        <div id="step2" class="hidden">
            <?= $form->field($model, 'comment')->textarea(['rows' => 12]) ?>
            <?= $form->field($model, 'attachments')->widget(UploadWidget::className(), [
                'multiple' => true,
                'uploadUrl' => ['report/au.upload.answer.attachment'],
                'containerOptions' => ['class' => 'form-group'],
                'uploadsContainer' => 'au-attachment-container',
                'uploadedSelector' => '#au-attachment-container .attachment-entry',
                'templateSelectors' => [
                    'filename' => '.au-file-name',
                    'delete' => '.au-delete-button',
                ],
                'options' => [
                    'accept' => 'image/*, application/pdf',
                ],
                'progressContainer' => '#au-progress-container',
                'progressbar' => '#au-progress-bar',
                'fileTemplate' => <<<EOT
            <div class="row attachment-entry">
                <div class="col-md-11">
                    <div class="au-file-name"></div>
                </div>
                <div class="col-md-1">
                    <button class="au-delete-button btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button>
                </div>
            </div>
EOT
                ,
            ]) ?>
            <div id="au-attachment-container"></div>
            <div id="au-progress-container">
                <div id="au-progress-bar"></div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <span>
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        </span>
        <span id="next">
        <?= Html::a(Yii::t('button', 'next'), '#', ['class' => 'btn btn-primary btn-answer-upload-next']) ?>
        </span>
        <span id="submit" class="hidden">
        <?= Html::submitButton(Yii::t('button', 'save'), [
            'class' => 'btn btn-primary btn-reload-pjax-submit',
            'data-url' => Url::to(['report/answer', 'id' => $model->report->id]),
            'data-pjax-container' => '#activity-list',
            'data-modal' => '#answer-modal',
        ]) ?>
        </span>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    '$(\'#au_file_answerform-attachments\').on(\'ajaxuploadstart\', function () {
            $("#upload-answer-ajax :button").attr("disabled", true);
        });
        $(\'#au_file_answerform-attachments\').on(\'ajaxuploadsucceeded\', function () {
            $("#upload-answer-ajax :button").attr("disabled", false);
        });'
);
