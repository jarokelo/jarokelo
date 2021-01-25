<?php

use app\models\db\Admin;
use app\models\db\City;
use app\models\db\PrPage;
use app\models\forms\RegistrationForm;
use app\modules\admin\models\AdminForm;
use kartik\select2\Select2;
use app\components\jqueryupload\UploadWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\AdminForm $model */
/* @var string $title */

if (!isset($edit)) {
    $edit = true;
}

$this->title = $model->getFullName();
$this->params['breadcrumbs'] = [$this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['admin/index'], 'label' => Yii::t('menu', 'admin')];

?>

<div class="row">
        <?php $form = ActiveForm::begin([
            'id' => 'admin-update',
            'action' => ['admin/update', 'id' => $model->id],
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]); ?>

        <div class="col-md-4">
            <div class="block--grey">
                <h3><?= Yii::t('admin', 'update.personal_info') ?></h3>

                <?= $form->beginField($model, 'image_file_name') ?>
                <?= Html::activeLabel($model, 'image_file_name') ?>
                <br />
                <div class="row">
                    <div id="au-profile-picture-container" class="col-md-4 va--center">
                        <div class="profile-thumbnail au-thumbnail">
                            <figure class="preview">
                                <img src="<?= Admin::getPictureUrl($model) ?>" style="width: 75px; height: 75px;">
                            </figure>
                        </div>
                    </div>
                    <div class="col-md-7 va--center">
                        <span class="au-upload-button">
                            <span><?= Html::a(Yii::t('admin', 'update.upload'), '#') ?></span>
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

                <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="block--grey">
                <h3><?= Yii::t('admin', 'update.system_info') ?></h3>

                <?php if ($edit && Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN) { ?>

                    <h4><?= Yii::t('admin', 'update.city') ?></h4>
                    <?= $form->field($model, 'connectedCities')->widget(Select2::className(), [
                        'options' => [
                            'multiple' => true,
                            'placeholder' => '',
                        ],
                        'data' => City::availableCities(false, false),
                        'theme' => Select2::THEME_KRAJEE,
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])->label(false) ?>

                    <h4><?= Yii::t('admin', 'update.pr_page') ?></h4>
                    <?= $form->field($model, 'connectedPrPages')->widget(Select2::className(), [
                        'options' => [
                            'multiple' => true,
                            'placeholder' => '',
                        ],
                        'data' => PrPage::availablePrPages(),
                        'theme' => Select2::THEME_KRAJEE,
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])->label(false) ?>
                <?php } else { ?>
                    <div>
                        <?php
                        $cities = [];
                        foreach ($model->cities as $city) {
                            $cities[] = $city->name;
                        }
                        ?>
                        <?= implode(', ', $cities) ?>
                    </div>
                <?php } ?>

                <h4><?= Yii::t('data', 'admin.status') ?></h4>
                <?php if ($edit && Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN && $model->id != Yii::$app->user->id) { ?>
                    <?= $form->field($model, 'status')->widget(Select2::className(), [
                        'data' => AdminForm::statuses(),
                        'theme' => Select2::THEME_KRAJEE,
                        'options' => [
                            'class' => 'load-status-note',
                        ],
                    ])->label(false) ?>
                <?php } else { ?>
                    <?= Yii::t(
                        'admin',
                        $model->status == Admin::STATUS_ACTIVE ? 'status.active' : ($model->status == Admin::STATUS_INACTIVE ? 'status.inactive' : (Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN ? 'status.super_admin' : 'status.active'))
                    ); ?>
                <?php } ?>

                <?php if ($edit && Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN && $model->id != Yii::$app->user->id) { ?>
                <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
                    <div class="form-group"><p><div class="display-status-note alert alert-warning"><?php echo Yii::t('data', 'superadmin-note-text') ?></div></p></div>
                <?php } ?>

                <h4><?= Yii::t('admin', 'update.permission') ?></h4>

                <?php Pjax::begin([
                    'id' => 'admin-permissions',
                    'formSelector' => '#permission-form',
                    'options' => [
                        'class' => 'pjax-hide-modal',
                        'data-modal' => '#permission-modal',
                    ],
                ]) ?>

                <?php foreach (Admin::permissions() as $lang => $permissions): ?>
                    <?php
                    $data = [];

                    foreach ($permissions as $permission => $permLang) {
                        if ($model->hasPermission($permission)) {
                            $data[] = strtolower(Yii::t('app', $permLang));
                        }
                    } ?>
                    <?php if (!empty($data)): ?>

                        <b><?= Yii::t('const', $lang) ?>:</b>
                        <ul>
                            <li><?= implode('</li><li>', $data) ?></li>
                        </ul>
                    <?php endif ?>
                <?php endforeach ?>

                <?php Pjax::end() ?>

                <?php if ($model->id != Yii::$app->user->id && Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN) { ?>
                    <div>
                        <?= Html::a(
                            Html::tag('span', '', ['class' => 'glyphicon glyphicon-edit']) .
                            Yii::t('admin', 'update.edit_permission'),
                            ['admin/permission', 'id' => $model->id],
                            [
                                'class' => 'btn btn-default btn-modal-content',
                                'data-modal' => '#permission-modal',
                                'data-url' => Url::to(['admin/permission', 'id' => $model->id]),
                                'data-target' => '#permission-modal-body',
                            ]
                        ) ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <?php $scoreData = $model->getScoreData() ?>
        <div class="col-md-4">
            <div class="block--grey">
                <h3><?= Yii::t('admin', 'update.activity') ?></h3>

                <div class="user__reports text-center">
                    <span class="total"><?= $scoreData['total'] ?></span><?= Yii::t('admin', 'update.score') ?><br/>
                    <span class="fs-medium"><?= Yii::t('admin', 'update.rank', ['rank' => $model->getRank()]) ?></span><br/>
                    <span class="fs-medium"><?= Html::a(
                        Yii::t('admin', 'update.activity_help'),
                        '#',
                        ['data-toggle' => 'modal', 'data-target' => '#activity-help-modal']
                    ) ?></span>
                </div>
                <div class="user__statistics">
                    <b><?= $scoreData['editing'] ?></b> <?= Yii::t('admin', 'activity.new_report') ?><br/>
                    <b><?= $scoreData['send'] ?></b> <?= Yii::t('admin', 'activity.answer_request') ?><br/>
                    <b><?= $scoreData['request'] ?></b> <?= Yii::t('admin', 'activity.response_request') ?><br/>
                    <b><?= $scoreData['resolve'] ?></b> <?= Yii::t('admin', 'activity.resolve') ?>
                </div>
            </div>

            <?php if (isset($adminPasswordForm)): ?>
            <?php $form = ActiveForm::begin([
                'id' => 'admin-password-update',
            ]);
            /** @var \app\modules\admin\models\AdminPasswordForm $adminPasswordForm */
            ?>
            <div class="block--grey">
                <h3><?= Yii::t('user', 'update.password_change') ?></h3>
                <p>
                    <b><?php echo Yii::t('profile', 'your_password_must_be'); ?></b>
                </p>
                <ul class="profile_ul list--validate" validate="#<?= Html::getInputId($adminPasswordForm, 'new_password') ?>">
                    <li valid="<?= RegistrationForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
                    <li valid="<?= RegistrationForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
                    <li valid="<?= RegistrationForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
                </ul>
                <br>
                <?= $form->field($adminPasswordForm, 'new_password')->passwordInput() ?>
                <?= $form->field($adminPasswordForm, 'repeat_password')->passwordInput() ?>
                <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
</div>

<?php if (Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN): ?>
    <!-- Modal -->
    <div class="modal fade" id="permission-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="permission-modal-body">
        </div>
    </div>
<?php endif ?>

<!-- Activity Help Modal -->
<div class="modal fade" id="activity-help-modal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" id="activity-help-modal-body">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= Yii::t('admin', 'update.activity_score') ?></h4>
            </div>

            <div class="modal-body">
                <p><?= Yii::t('admin', 'update.activity_help_1') ?></p>
                <p><?= Yii::t('admin', 'update.activity_help_2') ?></p>
                <p><?= Yii::t('admin', 'update.activity_help_3') ?></p>
            </div>
        </div>

    </div>
</div>
