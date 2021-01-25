<?php

use app\models\db\User;

use app\models\forms\NewPasswordForm;
use app\components\jqueryupload\UploadWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\User $model */
/* @var string $title */

if (!isset($edit)) {
    $edit = true;
}

$this->title = $model->getFullName();
$this->params['breadcrumbs'] = [$this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['user/index'], 'label' => Yii::t('menu', 'user')];

$reportData = $model->getReportData();

?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                'id' => 'user-update',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
            ]); ?>
            <div class="block--grey">
                <h3><?= Yii::t('user', 'update.personal_info') ?></h3>

                <?= $form->beginField($model, 'image_file_name') ?>
                <?= Html::activeLabel($model, 'image_file_name') ?>
                <br><br>
                <div class="row">
                    <div id="au-profile-picture-container" class="col-md-2 va--center">
                        <div class="profile-thumbnail au-thumbnail">
                            <figure class="preview">
                                <img src="<?= User::getPictureUrl($model) ?>" style="width: 75px; height: 75px;">
                            </figure>
                        </div>
                    </div><!--
                    --><div class="col-md-10 va--center">
                        <span class="au-upload-button">
                            <span><?= Html::a(Yii::t('user', 'update.upload'), '#') ?></span>
                            <?= UploadWidget::widget([
                                'model' => $model,
                                'attribute' => 'image_file_name',
                                'uploadUrl' => ['au.upload'],
                                'multiple' => false,
                                'preview' => [75, 75],
                                'containerOptions' => ['class' => 'form-group'],
                                'uploadsContainer' => 'au-profile-picture-container',
                                'uploadedSelector' => '#au-profile-picture-container .au-thumbnail',
                                'templateSelectors' => [
                                    'preview' => '.preview',
                                    'retry' => '.retryButton',
                                    'delete' => '.deleteButton',
                                    'error' => '.error',
                                ],
                                'options' => ['accept' => 'image/*'],
                                'fileTemplate' => <<<EOT
            <div class="profile-thumbnail au-thumbnail">
                <figure class="preview"></figure>
            </div>
EOT
                                ,
                            ]) ?>
                        </span>
                    </div>
                </div>
                <?= $form->endField() ?>

                <?= $form->field($model, 'last_name')->textInput() ?>
                <?= $form->field($model, 'first_name')->textInput() ?>
                <?= $form->field($model, 'email')->textInput() ?>
                <?= $form->field($model, 'phone_number')->textInput() ?>
                <?= $form->field($model, 'status')->radioList(User::statuses()) ?>

                <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
            </div>

            <div class="block--grey">
                <h3><?= Yii::t('user', 'update.api') ?></h3>
                <?php if (strlen(trim($model->api_token)) > 0): ?>
                    <?= $form->field($model, 'api_token')->textInput(['onclick' => 'this.select()', 'readonly' => 'readonly']) ?>
                    <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-refresh']) . Yii::t('button', 'api.reset'), ['user/api-generate', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                    <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']) . Yii::t('button', 'api.revoke'), ['user/api-revoke', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                <?php else: ?>
                    <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) . Yii::t('button', 'api.generate'), ['user/api-generate', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>


        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('user', 'update.reports') ?></h3>
                <div class="user__reports text-center">
                    <?= Html::a($reportData['total'], Url::to(['report/user', 'id' => $model->id]), ['class' => 'total']); ?>
                    <?= Yii::t('user', 'update.report') ?>
                </div>
                <div class="row user__statistics">
                    <div class="col-md-6">
                        <div>
                            <b><?= $reportData['open'] ?></b> <?= Yii::t('user', 'report.new_report') ?>
                        </div>
                        <div>
                            <b><?= $reportData['waiting_for_info'] ?></b> <?= Yii::t('user', 'report.waiting_for_info') ?>
                        </div>
                        <div>
                            <b><?= $reportData['waiting_for_response'] ?></b> <?= Yii::t('user', 'report.waiting_for_response') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <b><?= $reportData['waiting_for_answer'] ?></b> <?= Yii::t('user', 'report.waiting_for_answer') ?>
                        </div>
                        <div>
                            <b><?= $reportData['waiting_for_solution'] ?></b> <?= Yii::t('user', 'report.waiting_for_solution') ?>
                        </div>
                        <div>
                            <b><?= $reportData['unresolved'] ?></b> <?= Yii::t('user', 'report.unresolved') ?>
                        </div>
                        <div>
                            <b><?= $reportData['resolved'] ?></b> <?= Yii::t('user', 'report.resolved') ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$model->hasSocialAuth()): ?>
            <?php $form = ActiveForm::begin([
                'id' => 'user-password-update',
            ]);
            /** @var $passwordForm */
            ?>
            <div class="block--grey">
                <h3><?= Yii::t('user', 'update.password_change') ?></h3>
                <p><?= Yii::t('auth', 'password-requirements-label') ?></p>
                <ul class="profile_ul list--validate" validate="#<?= Html::getInputId($passwordForm, 'new_password') ?>">
                    <li valid="<?= NewPasswordForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
                    <li valid="<?= NewPasswordForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
                    <li valid="<?= NewPasswordForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
                </ul><br>
                <?= $form->field($passwordForm, 'new_password')->passwordInput() ?>
                <?= $form->field($passwordForm, 'repeat_password')->passwordInput() ?>
                <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>


    </div>
</div>
