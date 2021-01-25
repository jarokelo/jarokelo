<?php

use app\models\db\PrPage;
use app\models\db\Admin;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\components\jqueryupload\UploadWidget;
use app\components\helpers\Link;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\AdminForm $model */
/* @var string $title */

if (!isset($edit)) {
    $edit = true;
}

$this->title = $model->title;
$this->params['breadcrumbs'] = [$this->title];

if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_VIEW) && Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['pr-page/index'], 'label' => Yii::t('menu', 'pr_page')];
} else {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['institution/index'], 'label' => Yii::t('menu', 'institution')];
}

?>

<div class="row">
    <?php $form = ActiveForm::begin([
        'id' => 'pr-page-update',
        'action' => ['pr-page/update', 'id' => $model->id],
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>

    <div class="">
        <div class="col-md-12">
            <div class="col-md-10">
                <h3><?= $this->title ?></h3>
            </div>
            <div class="col-md-2 text-right">
                <a class="btn btn-primary" href="<?= Url::to(['pr-page-news/index', 'id' => $model->id]) ?>">
                    <?= Yii::t('pr_page', 'button.news') ?>
                </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('pr_page', 'update.title.introduction') ?></h3>
                <?= $form->field($model, 'title')->textInput() ?>
                <?= $form->field($model, 'introduction')->textarea(['rows' => 8])?>
            </div>
        </div>


        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('pr_page', 'update.title.connection') ?></h3>
                <?= $form->field($model, 'info_web_page')->textInput() ?>
                <?= $form->field($model, 'info_email')->textInput() ?>
                <?= $form->field($model, 'info_phone')->textInput() ?>
                <?= $form->field($model, 'info_address')->textInput() ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('pr_page', 'update.title.appearance') ?></h3>
                <?= $form->field($model, 'custom_color')->textInput() ?>
                <?= $form->field($model, 'video_url')->textInput() ?>
                <?= $form->field($model, 'social_feed_url')->textInput() ?>

                <?= $form->beginField($model, 'logo_file_name') ?>
                <?= Html::activeLabel($model, 'logo_file_name') ?>
                <br />
                <div class="row">
                    <div id="au-logo-picture-container" class="col-md-4 va--center">
                        <?php if ($model->logo_file_name) { ?>
                            <div class="au-thumbnail">
                                <figure class="logo-preview">
                                    <img src="<?= PrPage::getLogoUrl($model) ?>">
                                </figure>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-7 va--center">
                        <span class="au-upload-button">
                            <span><?= Html::a(Yii::t('pr_page', 'button.upload_image'), '#') ?></span>
                            <?= UploadWidget::widget([
                                'model' => $model,
                                'attribute' => 'logo_file_name',
                                'uploadUrl' => ['au.upload.logo'],
                                'multiple' => false,
                                'preview' => [75, 75],
                                'containerOptions' => ['class' => 'form-group'],
                                'uploadsContainer' => 'au-logo-picture-container',
                                'uploadedSelector' => '#au-logo-picture-container .au-thumbnail',
                                'templateSelectors' => [
                                    'preview' => '.logo-preview',
                                    'retry' => '.retryButton',
                                    'delete' => '.deleteButton',
                                    'error' => '.error',
                                ],
                                'options' => ['accept' => 'image/*'],
                                'fileTemplate' => <<<EOT
            <div class="au-thumbnail">
                <figure class="logo-preview"></figure>
            </div>
            
EOT
                                ,
                            ]) ?>
                        </span>
                    </div>
                </div>
                <?= $form->endField() ?>

                <?= $form->beginField($model, 'cover_file_name') ?>
                <?= Html::activeLabel($model, 'cover_file_name') ?>
                <br />
                <div class="row">
                    <div id="au-cover-picture-container" class="col-md-4 va--center">
                        <?php if ($model->cover_file_name) { ?>
                            <div class="au-thumbnail">
                                <figure class="preview">
                                    <img src="<?= PrPage::getCoverUrl($model) ?>">
                                </figure>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-7 va--center">
                        <span class="au-upload-button">
                            <span><?= Html::a(Yii::t('pr_page', 'button.upload_image'), '#') ?></span>
                            <?= UploadWidget::widget([
                                'model' => $model,
                                'attribute' => 'cover_file_name',
                                'uploadUrl' => ['au.upload.cover'],
                                'multiple' => false,
                                'preview' => [75, 75],
                                'containerOptions' => ['class' => 'form-group'],
                                'uploadsContainer' => 'au-cover-picture-container',
                                'uploadedSelector' => '#au-cover-picture-container .au-thumbnail',
                                'templateSelectors' => [
                                    'preview' => '.cover-preview',
                                    'retry' => '.retryButton',
                                    'delete' => '.deleteButton',
                                    'error' => '.error',
                                ],
                                'options' => ['accept' => 'image/*'],
                                'fileTemplate' => <<<EOT
            <div class="au-thumbnail">
                <figure class="cover-preview"></figure>
            </div>
            
EOT
                                ,
                            ]) ?>
                        </span>
                    </div>
                </div>
                <?= $form->endField() ?>

            </div>
        </div>

        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('pr_page', 'update.title.map') ?></h3>
                <?= $form->field($model, 'map_status')->widget(Select2::className(), [
                    'data' => PrPage::mapStatuses(),
                    'theme' => Select2::THEME_KRAJEE,
                ]) ?>

            </div>
        </div>

        <div class="col-md-6">
            <div class="block--grey">
                <h3><?= Yii::t('pr_page', 'update.title.settings') ?></h3>


                <div class="form-group field-prpage-custom_color">
                    <label class="control-label" for="pr-page-slug"><?= Yii::t('pr_page', 'update.url') ?></label>
                    <input type="text" id="pr-page-slug" class="form-control" value="<?= Link::to([Link::HOME, Link::PR_PAGE]) . '/' . $model->slug?>" readonly>

                    <p class="help-block help-block-error"></p>
                </div>

                <?= $form->field($model, 'status')->widget(Select2::className(), [
                    'data' => PrPage::statuses(),
                    'theme' => Select2::THEME_KRAJEE,
                ]) ?>
            </div>
        </div>

        <div class="col-md-6 text-right">
            <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
            <?php if (Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN || Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_DELETE)): ?>
                <?= Html::a(
                    Yii::t('button', 'delete'),
                    ['pr-page/delete', 'id' => $model->id],
                    [
                        'title' => Yii::t('yii', 'Delete'),
                        'class' => 'btn-modal-content btn btn-danger',
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-url' => Url::to(['pr-page/delete', 'id' => $model->id]),
                        'data-target' => '#pr-page-delete-modal-body',
                        'data-modal' => '#pr-page-delete-modal',
                        'data-pjax' => '0',
                    ]
                ) ?>
            <?php endif ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<?php if (Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="pr-page-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="pr-page-delete-modal-body"></div>
    </div>
<?php endif ?>