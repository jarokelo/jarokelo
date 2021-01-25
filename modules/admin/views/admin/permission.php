<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\Admin $admin */
/* @var \app\modules\admin\models\PermissionForm $model */

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'permission-form',
        'method' => 'post',
        'enableClientValidation' => false,
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('admin', 'update.edit_permission') ?></h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <h4><?= Yii::t('const', 'admin.permission.report') ?></h4>
                <?= $form->field($model, 'reportEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'reportDelete')->checkbox()->label(Yii::t('const', 'admin.permission.delete')) ?>
                <?= $form->field($model, 'reportStatistics')->checkbox()->label(Yii::t('const', 'admin.permission.statistics')) ?>
                <br />

                <h4><?= Yii::t('const', 'admin.permission.admin') ?></h4>
                <?= $form->field($model, 'adminView')->checkbox()->label(Yii::t('const', 'admin.permission.view')) ?>
                <?= $form->field($model, 'adminEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'adminAdd')->checkbox()->label(Yii::t('const', 'admin.permission.add')) ?>
                <?= $form->field($model, 'adminDelete')->checkbox()->label(Yii::t('const', 'admin.permission.delete')) ?>
                <br />

                <h4><?= Yii::t('const', 'admin.permission.city') ?></h4>
                <?= $form->field($model, 'cityView')->checkbox()->label(Yii::t('const', 'admin.permission.view')) ?>
                <?= $form->field($model, 'cityEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'cityAdd')->checkbox()->label(Yii::t('const', 'admin.permission.add')) ?>
            </div>
            <div class="col-md-6">
                <h4><?= Yii::t('const', 'admin.permission.user') ?></h4>
                <?= $form->field($model, 'userView')->checkbox()->label(Yii::t('const', 'admin.permission.view')) ?>
                <?= $form->field($model, 'userEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'userDelete')->checkbox()->label(Yii::t('const', 'admin.permission.delete')) ?>
                <?= $form->field($model, 'userKill')->checkbox()->label(Yii::t('const', 'admin.permission.kill')) ?>
                <?= $form->field($model, 'userFullDataExport')->checkbox()->label(Yii::t('const', 'admin.permission.full_data_export')) ?>
                <br />

                <h4><?= Yii::t('const', 'admin.permission.institution') ?></h4>
                <?= $form->field($model, 'institutionView')->checkbox()->label(Yii::t('const', 'admin.permission.view')) ?>
                <?= $form->field($model, 'institutionEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'institutionAdd')->checkbox()->label(Yii::t('const', 'admin.permission.add')) ?>
                <?= $form->field($model, 'institutionDelete')->checkbox()->label(Yii::t('const', 'admin.permission.delete')) ?>
                <br />

                <h4><?= Yii::t('const', 'admin.permission.pr_page') ?></h4>
                <?= $form->field($model, 'prPageEdit')->checkbox()->label(Yii::t('const', 'admin.permission.edit')) ?>
                <?= $form->field($model, 'prPageDelete')->checkbox()->label(Yii::t('const', 'admin.permission.delete')) ?>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-pjax' => 0, 'data-dismiss' => 'modal'])?>
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end() ?>
</div>
