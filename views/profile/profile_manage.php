<?php

use app\components\helpers\Link;
use app\components\widgets\Pjax;
use app\models\db\City;
use app\models\forms\RegistrationForm;
use yii\helpers\Html;
use app\components\helpers\SVG;
use app\components\ActiveForm;
use app\components\jqueryupload\UploadWidget;

/** @var $passwordForm \app\models\forms\PasswordChangeForm */
/** @var $user \app\models\db\User */
/** @var $userInfoForm \app\models\forms\UserInfoChangeForm */
/** @var $city array */
/** @var $districts array */
/** @var $notificationTypes array */
/** @var $notifications array */

echo $this->render('_profile_top', [
    'user' => $user,
]);

$this->registerJs("
    site.message({
        text: '" . Yii::t('profile', 'profile-picture-updated') . "',
        ajaxComplete: 'UserInfoChangeForm[image_file_name]'
    });
");

?>
<div class="container">
    <div class="profile__formgroup">
        <h2 class="profile__title"><?= Yii::t('profile', 'modify_profile'); ?></h2>

        <?php Pjax::begin([
            'id' => 'report-list-container',
            'enablePushState' => true,
            'enableReplaceState' => true,
            'formSelector' => '#form-user-city-district',
            'linkSelector' => false,
        ]); ?>
        <?php
        if (Yii::$app->request->isPjax) {
            \app\components\AlertWidget::showAlerts();
        }
        ?>
        <?php $form1 = ActiveForm::begin([
            'id' => 'form-user-city-district',
            'action' => Link::to(Link::PROFILE_MANAGE),
            'method' => 'post',
        ]); ?>
        <div class="form__title">
            <span class="form__legend_icon">
                <?= SVG::icon(SVG::ICON_CITY, ['class' => 'icon filter__icon'])?>
            </span>
            <?= Yii::t('data', 'user.city_id'); ?>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-5">
                <?= $form1->field($user, 'city_id')->dropDownList(City::availableCities(true, false), ['prompt' => Yii::t('label', 'generic.choose')]); ?>
            </div>
            <?php if ($user->city_id && !empty($districts = \app\models\db\District::getAll($user->city_id))): ?>
            <div class="col-xs-12 col-md-5">
                <?= $form1->field($user, 'district_id')->dropDownList($districts, ['prompt' => Yii::t('label', 'generic.choose')]); ?>
            </div>
            <?php else: ?>
            <?php
                $user->district_id = null;
                echo Html::activeHiddenInput($user, 'district_id');
            ?>
            <?php endif; ?>
        </div>

        <p class="profile__hint"><?= Yii::t('profile', 'city_district_filtered_reports'); ?></p>
        <?php ActiveForm::end(); ?>

        <?php Pjax::end() ?>
    </div>

    <div class="profile__formgroup">
        <div class="form__title">
            <span class="form__legend_icon">
                <?= SVG::icon(SVG::ICON_EMAIL, ['class' => 'icon filter__icon'])?>
            </span>
            <?= Yii::t('profile', 'notifications'); ?>
        </div>

        <?php $formNotifications = ActiveForm::begin([
            'id' => 'user-notification-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => Link::to(Link::PROFILE_MANAGE),
            'method' => 'post',
        ]); ?>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-5 profile__formrow">
                <?= Yii::t('profile', 'send_notification'); ?><br /><br />

                <?php foreach ($notificationTypes as $notificationTypeId => $notificationType): ?>
                    <div class="row">
                        <div class="col-xs-12 profile__formrow">
                            <?=$formNotifications->field($user, $notificationTypeId, ['template' => '<label class="checkbox--label checkbox--wrap">{input}<div class="top checkbox--wrap"><div>' . $notificationType . '</div><div class="profile__hint">' . \app\models\db\User::getNotificationHint($notificationType) . '</div></div></label><br><br>{error}'])->checkbox(['value' => 1, 'checkbox-css' => 'top checkbox--left', 'label' => null]); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-5 profile__formrow">
                <?= Yii::t('profile', 'send_frequency'); ?><br /><br />
                <?= $formNotifications->field($user, 'notification')
                ->radioList($notifications, [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $return = '<div class="row"><div class="col-xs-12 profile__formrow"><label class="radio--label radio--wrap">';
                        $return .= '<input type="radio" radio-css="radio--left" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' />';
                        $return .= '<div class="middle radio--wrap"><div>' . $label . '</div>';
                        $return .= '<div class="profile__hint">' . \app\models\db\User::getNotificationHint($label) . '</div>';
                        $return .= '</div></label></div></div>';
                        return $return;
                    },
                ])
                ->label(false); ?>
            </div>
        </div>

        <div>
            <?= Html::submitButton(Yii::t('profile', 'save_changes'), ['class' => 'button button--green button--medium']); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="profile__formgroup">
        <div class="form__title">
            <span class="form__legend_icon">
                <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'icon filter__icon'])?>
            </span>
            <?= Yii::t('profile', 'profile-picture'); ?>
        </div>

        <?php $formManage = ActiveForm::begin([
            'id' => 'user-manage-form-avatar',
            'action' => Link::to(Link::PROFILE_MANAGE),
            'method' => 'post',
        ]); ?>

        <div class="row">
            <div class="col-xs-12 col-md-5">
                <div class="image">
                    <?= $formManage->beginField($user, 'image_file_name'); ?>
                    <div>
                        <div id="au-profile-picture-container">
                            <div class="profile-thumbnail au-thumbnail">
                                <figure class="preview">
                                    <img src="<?= \app\models\db\User::getPictureUrl($user) ?>" style="width: 75px; height: 75px;">
                                </figure>
                            </div>
                        </div>
                        <div>
                            <span class="au-upload-button">
                            <div class="upload__button"><?= Html::label(Yii::t('user', 'update.upload'), 'au_file_userinfochangeform-image_file_name', ['class' => 'button button--green button--medium']) ?></div>
                                <?= UploadWidget::widget([
                                    'model' => $userInfoForm,
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
                                ]); ?>
                                </span>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                        <?= $formManage->endField() ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="profile__formgroup">
        <div class="form__title">
            <span class="form__legend_icon">
                <?= SVG::icon(SVG::ICON_GENERAL_INFO, ['class' => 'icon filter__icon'])?>
            </span>
            <?= Yii::t('profile', 'personal_information'); ?>
        </div>

        <?php $formManage = ActiveForm::begin([
            'id' => 'user-manage-form',
            'action' => Link::to(Link::PROFILE_MANAGE),
            'method' => 'post',
        ]); ?>

        <div class="row">
            <div class="col-xs-12 col-md-5">
                <?= $formManage->field($userInfoForm, 'last_name')->textInput(); ?>
            </div>
            <div class="col-xs-12 col-md-5">
                <?= $formManage->field($userInfoForm, 'first_name')->textInput(); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-5">
                <?= $formManage->field($userInfoForm, 'email')->textInput(); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-5">
                <?= $formManage->field($userInfoForm, 'phone_number')->textInput(); ?>
            </div>
        </div>

        <?= Html::submitButton(Yii::t('profile', 'save_changes'), ['class' => 'button button--green button--medium']); ?>

        <?php ActiveForm::end(); ?>
    </div>

    <?php if (!$user->hasSocialAuth()): ?>
    <div class="profile__formgroup" id="change-password" name="change-password">
        <div class="form__title">
            <span class="form__legend_icon">
                <?= SVG::icon(SVG::ICON_LOCK, ['class' => 'icon filter__icon'])?>
            </span>
            <?= Yii::t('profile', 'modify_password'); ?>
        </div>
        <div class="col-md-5 col-xs-12">
            <?php if ($user->is_old_password): ?>
            <div class="alert alert-danger">
                <?php echo Yii::t('profile', 'old_password_alert'); ?>
            </div>
            <?php endif; ?>

            <?php $formPassword = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
            ]); ?>

            <?= $formPassword->field($passwordForm, 'old_password')->passwordInput(); ?>

            <?= $formPassword->field($passwordForm, 'new_password')->passwordInput(); ?>

            <?= $formPassword->field($passwordForm, 'repeat_password')->passwordInput(); ?>

            <p>
                <b><?php echo Yii::t('profile', 'your_password_must_be'); ?></b>
            </p>
            <ul class="profile_ul list--validate" validate="#<?= Html::getInputId($passwordForm, 'new_password') ?>">
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_LENGTH ?>"><?= Yii::t('auth', 'password-requirements-length') ?></li>
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_NUMBER ?>"><?= Yii::t('auth', 'password-requirements-number') ?></li>
                <li valid="<?= RegistrationForm::PASSWORD_REGEX_CAPITAL ?>"><?= Yii::t('auth', 'password-requirements-capital') ?></li>
            </ul>

            <?= Html::submitButton(Yii::t('profile', 'set_as_password'), ['class' => 'button button--green button--medium']); ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>

</div>
<?= $this->render('/_snippets/_hero-bottom-dual'); ?>
