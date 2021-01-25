<?php

use app\models\db\Institution;
use app\models\db\Rule;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var \app\models\db\City $city */
/* @var \app\models\db\City $streetGroup */
/* @var \app\models\db\Rule $model */
/* @var int[] $selectedContacts */

?>

<div class="modal-content">
    <?php $form = ActiveForm::begin([
        'id' => 'rule-create-ajax',
        'action' => ['city/rule', 'id' => $city->id, 'rid' => $model->id],
        'enableClientValidation' => true,
        'options' => [
            'data-pjax' => 1,
        ],
    ]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('rule', $model->isNewRecord ? 'create' : 'update') ?></h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'district_id')->widget(Select2::className(), [
            'data' => $city->getAvailableDistricts(),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'placeholder' => Yii::t('rule', 'placeholder.district'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>
        <?= $form->field($model, 'street_group_id')->widget(Select2::className(), [
            'data' => \app\models\db\StreetGroup::getList($city->id),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'placeholder' => Yii::t('rule', 'placeholder.street'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>
        <?= $form->field($model, 'report_category_id')->widget(Select2::className(), [
            'data' => \app\models\db\ReportCategory::getList(),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'placeholder' => Yii::t('rule', 'placeholder.category'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>
        <?= $form->field($model, 'institution_id')->widget(Select2::className(), [
            'data' => ArrayHelper::map(Institution::getInstitutions($city->id), 'id', 'name'),
            'theme' => Select2::THEME_KRAJEE,
            'options' => [
                'class' => 'load-institution-contacts',
                'placeholder' => Yii::t('rule', 'placeholder.institution'),
                'data-url' => Url::to(['institution/contact-list', 'id' => 'ph', 'rid' => $model->id]),
                'data-target' => '.contact-container',
            ],
        ]) ?>
        <p class="<?= $model->isNewRecord ? 'hidden' : '' ?> institution-note">
            <strong><?= Yii::t('rule', 'update.note') ?></strong>
            <blockquote><?= $model->isNewRecord || $model->institution_id === null || $model->institution === null ? '' : $model->institution->note ?></blockquote>
        </p>

        <p class="<?= $model->isNewRecord ? 'hidden' : '' ?> show-on-contact-load">
            <strong><?= Yii::t('rule', 'update.email_addresses') ?></strong>
        </p>
        <div class="<?= $model->isNewRecord ? 'hidden' : '' ?> show-on-contact-load contact-container">
            <?php if (!$model->isNewRecord && $model->institution_id !== null && $model->institution !== null): ?>
                <?= $this->render('@adminViews/institution/_contact_list', [
                    'contacts' => $model->institution->contacts,
                    'selectedContacts' => $selectedContacts,
                ]) ?>
            <?php endif ?>
        </div>

        <?= $form->field($model, 'status')->radioList(Rule::statuses()) ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
