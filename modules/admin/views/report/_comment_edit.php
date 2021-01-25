<?php

use app\modules\admin\models\AnswerForm;
use app\components\jqueryupload\UploadWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\CommentForm|\app\modules\admin\models\AnswerForm $model */

$answer = $model instanceof AnswerForm;

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'edit-comment-ajax',
        'action' => ['report/edit-comment', 'id' => $model->reportActivity->id],
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('report', 'report.edit.text') ?></h4>
    </div>

    <div class="modal-body">
        <?php if ($answer): ?>
            <div class="form-group">
                <label class="control-label" for="institution"><?= Yii::t('report', 'answer.institution') ?></label>
                <div id="institution"><?= \yii\helpers\ArrayHelper::getValue($model, 'institution.name', Yii::t('report', 'no-institution-name')) ?></div>
            </div>
        <?php endif ?>

        <div class="form-group field-<?php echo Html::getInputId($model, 'comment'); ?> required">
            <label class="control-label" for="<?php echo Html::getInputId($model, 'comment'); ?>"><?php echo Yii::t('report', 'report.comment.text') ?></label>
            <textarea id="<?php echo Html::getInputId($model, 'comment'); ?>" class="form-control" rows="12" name="<?php echo Html::getInputName($model, 'comment'); ?>"><?php echo Html::getAttributeValue($model, 'comment'); ?></textarea>
            <p class="help-block help-block-error"></p>
        </div>

        <div style="display:none;">
            <?= $form->field($model, 'comment')->textarea(['name' => 'hidden-comment', 'value' => '']) ?>
        </div>

        <?= $form->field($model, 'attachments')->widget(UploadWidget::className(), [
            'multiple' => true,
            'uploadUrl' => [$answer ? 'report/au.upload.answer.attachment' : 'report/au.upload.comment.attachment'],
            'containerOptions' => ['class' => 'form-group'],
            'uploadsContainer' => 'au-attachment-container',
            'uploadedSelector' => '#au-attachment-container .attachment-entry',
            'templateSelectors' => [
                'filename' => '.au-file-name',
                'delete' => '.au-delete-button',
            ],
            'options' => ['accept' => 'image/*'],
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
        <?= Html::hiddenInput(Html::getInputName($model, 'attachments[]')) ?>
        <div id="au-attachment-container">
            <?php if (is_array($model->attachments)): ?>
                <?php foreach ($model->attachments as $attachment): ?>
                    <div class="row attachment-entry">
                        <div class="col-md-11">
                            <div class="au-file-name"><?= $attachment ?></div>
                        </div>
                        <div class="col-md-1">
                            <button class="au-delete-button-static btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button>
                        </div>
                        <?= Html::hiddenInput(Html::getInputName($model, 'attachments[]'), $attachment) ?>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
