<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\db\CustomQuestion;
use app\modules\admin\assets\CustomQuestionAsset;
use dosamigos\tinymce\TinyMce;

/* @var $this yii\web\View */
/* @var $model app\models\db\CustomQuestion */
/* @var $form yii\widgets\ActiveForm */

CustomQuestionAsset::register($this);
?>
<?= $this->render('_style') ?>
<script>
    var ANSWER_OPTIONS = JSON.parse('<?= $model->answer_options ?>');
    var TYPE_RADIO = '<?= CustomQuestion::TYPE_RADIO_BUTTON ?>';
    var TYPE_CHECKBOX = '<?= CustomQuestion::TYPE_CHECKBOX ?>';
    var TYPE_SINGLE_SELECT_DROPDOWN = '<?= CustomQuestion::TYPE_SINGLE_SELECT_DROPDOWN ?>';
    var TYPE_LONG_TEXT = '<?= CustomQuestion::TYPE_LONG_TEXT_ANSWER ?>';
    var TYPE_LINEAR_SCALE = '<?= CustomQuestion::TYPE_LINEAR_SCALE ?>';
</script>
<div class="custom-form-form">

    <?php $form = ActiveForm::begin([
        'id' => 'custom-question',
    ]); ?>

    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->widget(
        TinyMce::class,
        [
            'options' => [
                'rows' => 25,
            ],
            'language' => 'en',
            'clientOptions' => [
                'force_br_newlines' => true,
                'force_p_newlines' => false,
                'forced_root_block' => '',
                'images_upload_url' => '/admin/tiny-mce/upload-file',
                'extended_valid_elements' => '*[*]',
                'automatic_uploads' => true,
                'plugins' => [
                    'advlist autolink lists link charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste image imagetools',
                ],
                'image_class_list' => [
                    [
                        'title' => 'Responsive',
                        'value' => 'img img-responsive',
                    ],
                ],
                'file_picker_types' => 'file, image, media',
                'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify |
                bullist numlist outdent indent | link image imageupload | fontselect | cut copy paste photoSwipeToggleButton',
            ],
        ]
    ) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusSelection()) ?>

    <?= $form->field($model, 'required')->checkbox() ?>

    <?= $form->field($model, 'type')->dropDownList($model::getQuestionTypes()) ?>

    <?= $this->render('_form/_long_text_answer') ?>
    <?= $this->render('_form/_radio_button') ?>
    <?= $this->render('_form/_checkbox') ?>
    <?= $this->render('_form/_single_select_dropdown') ?>
    <?= $this->render('_form/_linear_scale') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('custom_form', 'Mentés'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
