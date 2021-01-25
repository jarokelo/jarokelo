<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use app\models\db\PrPageNews;
use app\components\jqueryupload\UploadWidget;

/* @var \app\models\db\PrPageNews $model */

?>

<?php $form = ActiveForm::begin([
    'id' => 'news-create-ajax',
    'action' => $action,
    'enableClientValidation' => true,
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]); ?>


<?= $form->field($model, 'title')->textInput() ?>


<?= $form->field($model, 'text')->widget(CKEditor::className(), [
    'options' => ['rows' => 6],
    'preset' => 'custom',
    'clientOptions' => [
        'toolbarGroups' => [
            ['name' => 'undo'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'colors'],
            ['name' => 'links', 'groups' => ['links', 'insert']],
            ['name' => 'others', 'groups' => ['others', 'about']],
            ['name' => 'styles'],
        ],
        'removeButtons' => 'Image,About,Styles,Anchor,Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
        'removePlugins' => 'elementspath',
        'resize_enabled' => false,
    ],
]) ?>

<div class="hidden">
    <?= $form->field($model, 'pr_page_id')->hiddenInput(['value' => $model->pr_page_id])->label(''); ?>
</div>

<?= $form->field($model, 'status')->widget(Select2::className(), [
    'data' => PrPageNews::statuses(),
    'theme' => Select2::THEME_KRAJEE,
]) ?>

<?= $form->field($model, 'published_date')->widget(\kartik\date\DatePicker::className(), [
    'pluginOptions' => ['format' => 'yyyy-mm-dd'],
]) ?>

<?= $form->beginField($model, 'image_file_name') ?>
<?= Html::activeLabel($model, 'image_file_name') ?>
<br />

<div class="row">
        <div id="au-image-picture-container" class="col-md-4 va--center">
            <?php if ($model->image_file_name) { ?>
                <div class="au-thumbnail">
                    <figure class="image-preview">
                            <img src="<?= PrPageNews::getImageUrl($model) ?>">
                    </figure>
                </div>
            <?php } ?>
        </div>
    <div class="col-md-7 va--center">
                        <span class="au-upload-button">
                            <span><?= Html::a(Yii::t('pr_page_news', 'button.upload_image'), '#') ?></span>
                            <?= UploadWidget::widget([
                                'model' => $model,
                                'attribute' => 'image_file_name',
                                'uploadUrl' => ['au.upload'],
                                'multiple' => false,
                                'preview' => [75, 75],
                                'containerOptions' => ['class' => 'form-group'],
                                'uploadsContainer' => 'au-image-picture-container',
                                'uploadedSelector' => '#au-image-picture-container .au-thumbnail',
                                'templateSelectors' => [
                                    'preview' => '.image-preview',
                                    'retry' => '.retryButton',
                                    'delete' => '.deleteButton',
                                    'error' => '.error',
                                ],
                                'options' => ['accept' => 'image/*'],
                                'fileTemplate' => <<<EOT
            <div class="au-thumbnail">
                <figure class="image-preview"></figure>
            </div>
            
EOT
                                ,
                            ]) ?>
                        </span>
    </div>
</div>
<?= $form->endField() ?>
