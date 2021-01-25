<?php

use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\models\db\City;
use app\models\db\ReportCategory;
use app\models\db\PrPage;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use app\components\ActiveForm;
use app\assets\AppAsset;
use app\assets\ButtonAsset;
use app\models\db\Report;

// Hotjar tracking code
if (YII_CONFIG_ENVIRONMENT !== 'development') {
    $this->registerJs(<<<SCRIPT
(function(h,o,t,j,a,r){
    h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:269852,hjsv:5};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');

SCRIPT
        , View::POS_HEAD);
}

$slug = \Yii::$app->request->get('citySlug');
$bundle = AppAsset::register($this);
ButtonAsset::register($this);
?>

<aside class="hero hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="hidden--mobile">
            <div class="hero__background hero--volunteer"></div>
        </div>
        <div class="hidden--desktop">
            <div class="hero__background hero--volunteer"></div>
        </div>
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-10 col-sm-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('report', $slug . '.h1') ?></h2>
                    <p class="hero__lead"><?= Yii::t('report', $slug . '.text.main') ?></p>
                    <p class="hero__lead hero__secondary"><?= Yii::t('report', $slug . '.text.secondary') ?></p>
                    <img
                            src="<?= $bundle->baseUrl ?>/images/<?= $slug ?>_icon.png"
                            height="190"
                            width="190"
                            style="margin: 0 auto;"
                            alt="<?= Yii::t('report', $slug) ?>"
                    >
                </div>
            </div>
        </div>
    </div>
</aside>

<?php
$fallbackLatitude = \yii\helpers\ArrayHelper::getValue($model, 'city.latitude', Yii::$app->params['map']['defaultPosition']['lat']);
$fallbackLongitude = \yii\helpers\ArrayHelper::getValue($model, 'city.longitude', Yii::$app->params['map']['defaultPosition']['lng']);

/* @var \yii\web\View $this */
/* @var \app\models\forms\ReportForm $model */
/* @var \app\components\ActiveForm $form  */
/* @var PrPage $prPageModel  */

echo $this->render('/_snippets/_mapbox', [
    'options' => [
        'zoom' => 16,
        'selectors' => [
            'zoom' => '#reportform-zoom',
            'show_on_map' => '[show-on-map]',
            'show_me_on_map' => '[show-me-on-map]',
            'map' => '#map',
            'latitude' => '#reportform-latitude',
            'longitude' => '#reportform-longitude',
            'user_location' => '#reportform-user_location',
            'post_code' => '#reportform-post_code',
            'address' => '#reportform-address',
            'street_name' => '#reportform-street_name',
        ],
        'center' => [
            'lat' => \app\models\db\Report::formatCoordinate((empty($model->latitude) ? $fallbackLatitude : $model->latitude)),
            'lng' => \app\models\db\Report::formatCoordinate((empty($model->longitude) ? $fallbackLongitude : $model->longitude)),
        ],
        'locationChangeHandler' => true,
        'enableGeoCodeService' => true,
    ],
]);

$form = ActiveForm::begin([
    'id' => 'report-create-form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'options' => [
        'class' => 'init-loader',
        'autocomplete' => 'off',
    ],
]);

$this->registerJs("$('#template-source').data('input-template', " . Json::encode((string)$form->field($model, 'videos[]')->hiddenInput(['id' => null])->label(false)) . ');');
$this->registerJs("$('#template-source').data('close-template', " . Json::encode((string)SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon'])) . ');');

if ($anonymous = Yii::$app->session->get('anonymous')) {
    // report will be saved as anonymous by preparing form field
    $model->anonymous = $anonymous;
    $this->registerJs(
        "$('.checkbox').addClass('checkbox--checked');",
        View::POS_READY
    );
}

if ($slug && in_array($slug, $projects = array_flip(Report::$projects))) {
    echo $form->field(
        $model,
        'project'
    )
        ->hiddenInput(['value' => $projects[$slug]])
        ->label(false);
}

if ($prPageModel): ?>
    <div class="sticky-header">
        <div class="container">
            <div class="row" >
                <div class="col-md-6 col-xs-4 align-middle">
                    <h1 class="logo ">
                        <a href="<?= Link::to([Link::PR_PAGE, $prPageModel->slug]) ?>">
                            <img src="<?= PrPage::getLogoUrl($prPageModel) ?>" style="height: 3em;">
                        </a>
                    </h1>
                </div>
                <div class="col-md-6 col-xs-8">
                    <div class="sticky-header__button text-right">
                        <a class="sticky-header__button-link" style="--color: <?= $prPageModel->custom_color ?>;" href="<?= Link::to([Link::PR_PAGE, $prPageModel->slug]) ?>">
                            <?= Yii::t('report', 'button.back_to_pr_page') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div style="height: 0.3em; background-color: <?= $prPageModel->custom_color ?>;"></div>
    </div>
<?php endif ?>

    <div id="template-source"></div>
    <div class="error error__sum" style="display:none;">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <?= Yii::t('app', 'error_in_form_submit'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container form form--padding report--issue">
        <div class="sticky-header__after"></div>
        <div class="row">
            <div class="col-xs-12">
                <?php if ($prPageModel) {?>
                    <h2 class="heading heading--5"><?= Yii::t('report', 'create.title_for', ['institution' => $prPageModel->institution->name]); ?></h2>
                <?php } else { ?>
                    <h2 class="heading heading--5"><?= Yii::t('report', 'create.title'); ?></h2>
                <?php } ?>
            </div>
        </div>

        <?php if (Yii::$app->user->isGuest): ?>
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="report">
                        <div class="form__row">
                            <div class="institution-info">
                                <div class="institution-info__media">
                                    <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'icon institution-info__icon--1']) ?>
                                </div>
                                <div class="institution-info__text">
                                    <p class="heading"><?= Yii::t('report', 'log_in'); ?></p>
                                    <p><?= Yii::t('report', 'login_for_faster_report'); ?></p>
                                    <p>
                                        <span class="row middle-xs center-xs start-lg">
                                            <span class="col-xs-12 col-lg-3">
                                                  <?= Html::a(Yii::t('button', 'login2'), Link::to(Link::LOGIN_FROM_NEW_REPORT), ['class' => 'button button--medium button--green']) ?>
                                            </span>
                                            <span class="col-xs-12 col-lg-9">
                                                <br class="hidden--desktop">
                                                <?= Yii::t('report', 'no_account_yet'); ?> <?= Html::a(Yii::t('button', 'register!'), Link::to(Link::AUTH_REGISTER), ['class' => 'link link--info']) ?>
                                            </span>
                                        </span>
                                    </p>
                                </div>
                                <a class="institution-info__close" click-hide=".row">
                                    <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon filter__icon']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="steps">
            <div class="steps__icon" show-step="1">
                <?= SVG::icon(SVG::ICON_GENERAL_INFO, ['class' => 'icon filter__icon'])?>
            </div>
            <div class="steps__dash"></div>
            <div class="steps__icon" show-step="2">
                <?= SVG::icon(SVG::ICON_POI, ['class' => 'icon filter__icon'])?>
            </div>
            <div class="steps__dash"></div>
            <div class="steps__icon" show-step="3">
                <?= SVG::icon(SVG::ICON_IMAGES, ['class' => 'icon filter__icon'])?>
            </div>
            <div class="steps__dash"></div>
            <div class="steps__icon" show-step="4">
                <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'icon filter__icon'])?>
            </div>
            <div class="steps__dash"></div>
            <div class="steps__icon" show-step="final">
                <?= SVG::icon(SVG::ICON_FLAG, ['class' => 'icon filter__icon'])?>
            </div>
        </div>

        <section class="step" step="1">
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="form__group form__asset">
                        <div class="form__title">
                            <span class="form__legend_icon step__hidden">
                                <?= SVG::icon(SVG::ICON_GENERAL_INFO, ['class' => 'icon filter__icon'])?>
                            </span>
                            <?= Yii::t('label', 'report.generic_information') ?>
                            <span class="form__legend_icon step__helper step__visible">
                                <?= SVG::icon(SVG::ICON_WHAT, ['class' => 'icon filter__icon'])?>
                            </span>
                            <span class="form__legend_icon step__edit step__partial--hidden hidden--desktop" show-step="1">
                                <?= SVG::icon(SVG::ICON_PEN, ['class' => 'icon filter__icon'])?>
                            </span>
                        </div>

                        <div>
                            <div class="visuallyhidden"><?= $form->field($model, 'id')->hiddenInput()->label(false) ?></div>
                            <?php
                            $field = $form->field($model, 'report_category_id')
                                ->dropDownList(
                                    ReportCategory::getList(),
                                    ['prompt' => Yii::t('report', 'create.select_category')],
                                    ['class' => 'select select--default select--full step__final--hidden hidden']
                                );
                            ?>
                            <?= $field ?>
                            <input type="text" class="step__final" readonly sync="#reportform-report_category_id">
                            <?= $form->field($model, 'name')->textInput(['class' => 'input input--default step__final--hidden']) ?>
                            <input type="text" class="step__final" readonly sync="#reportform-name">
                            <?= $form->field($model, 'description')->textarea(['class' => 'input input--default step__final--hidden', 'rows' => '8']) ?>
                            <div class="step__final" readonly sync="#reportform-description"></div>
                        </div>
                    </div>

                    <div class="form__row hidden--desktop">
                        <button type="button" class="button button--large button--submit" show-step="2"><?= Yii::t('report', 'step.next') ?></button>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-4 step__help">
                    <div class="panel panel--info panel--title-offset panel--label-offset">
                        <div class="panel__body">
                            <p><?= Yii::t('report', 'create.howtofill.title'); ?></p>
                            <p><?= Yii::t('report', 'create.howtofill.description')?></p>
                            <p><?= Yii::t('report', 'create.howtofill.patient'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="step" step="2">
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="form__group form__asset">
                        <div class="form__title">
                            <span class="form__legend_icon step__hidden">
                                <?= SVG::icon(SVG::ICON_POI, ['class' => 'icon filter__icon']) ?>
                            </span>
                            <?= Yii::t('label', 'report.location') ?>
                            <span class="form__legend_icon step__helper step__visible">
                                <?= SVG::icon(SVG::ICON_WHAT, ['class' => 'icon filter__icon'])?>
                            </span>
                            <span class="form__legend_icon step__edit step__partial--hidden hidden--desktop" show-step="2">
                                <?= SVG::icon(SVG::ICON_PEN, ['class' => 'icon filter__icon'])?>
                            </span>
                        </div>

                        <div class="step__final--hidden">
                            <?= $form->field($model, 'city_id')->dropDownList(City::availableCities(true, false), ['prompt' => Yii::t('report', 'create.select_city')], ['class' => 'select select--default select--full', 'id' => 'city-dropdown']) ?>
                        </div>
                        <div class="row bottom-xs">
                            <div class="col-xs-12 col-lg-8 user-location-container">
                                <?= $form->field($model, 'user_location')->textInput(['class' => 'input input--default step__final--hidden']) ?>
                                <input type="text" class="step__final" readonly sync="#reportform-user_location">
                                <button type="button" class="button user_location__clear hidden--mobile">
                                    <?= SVG::icon(SVG::ICON_CLOSE_WHITE, ['class' => 'icon'])?>
                                </button>
                            </div>
                            <div class="col-xs-12 col-lg-4 center-xs step__final--hidden">
                                <div class="form__row">
                                    <button type="button" class="button button--solid button--round-icon button--red user_location__clear hidden--desktop">
                                        <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon'])?>
                                    </button>
                                    <button type="button" class="button button--success button--solid button--full hidden--mobile show-on-map" show-on-map>
                                        <?= Yii::t('report', 'button.show_on_map') ?>
                                    </button>
                                    <button type="button" class="button button--green button--solid button--round-icon hidden--desktop" show-me-on-map>
                                        <?= SVG::icon(SVG::ICON_TARGET, ['class' => 'icon'])?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($prPageModel) { ?>
                            <?= $form->field($model, 'institution_id')->hiddenInput(['value' => $prPageModel->institution->id])->label(false)->error(false) ?>
                        <?php } ?>
                        <?= $form->field($model, 'zoom')->hiddenInput()->label(false)->error(false) ?>
                        <?= $form->field($model, 'latitude')->hiddenInput()->label(false)->error(false) ?>
                        <?= $form->field($model, 'longitude')->hiddenInput()->label(false)->error(false) ?>
                        <?= $form->field($model, 'post_code')->hiddenInput()->label(false)->error(false) ?>
                        <?= $form->field($model, 'street_name')->hiddenInput()->label(false)->error(false) ?>
                        <?= $form->field($model, 'address')->hiddenInput()->label(false)->error(false) ?>

                        <div class="form__row">
                            <div class="col-xs-12">
                                <div id="map" class="report__map row step__final--notouch"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form__row hidden--desktop">
                        <button type="button" class="button button--large button--submit" show-step="3"><?= Yii::t('report', 'step.next') ?></button>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-4 step__help">
                    <div class="panel panel--info panel--title-offset panel--label-offset">
                        <div class="panel__body">
                            <p><?= Yii::t('report', 'create.howtofind.title'); ?></p>
                            <p><?= Yii::t('report', 'create.howtofind.location'); ?></p>
                            <p><?= Yii::t('report', 'create.howtofind.address'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="step" step="3">
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="form__group form__asset">
                        <div class="form__title">
                            <span class="form__legend_icon step__hidden">
                                <?= SVG::icon(SVG::ICON_IMAGES, ['class' => 'icon filter__icon'])?>
                            </span>
                            <?= Yii::t('label', 'report.pictures') ?>
                            <span class="form__legend_icon step__helper step__visible">
                                <?= SVG::icon(SVG::ICON_WHAT, ['class' => 'icon filter__icon'])?>
                            </span>
                            <span class="form__legend_icon step__edit step__partial--hidden hidden--desktop" show-step="3">
                                <?= SVG::icon(SVG::ICON_PEN, ['class' => 'icon filter__icon'])?>
                            </span>
                        </div>
                        <div class="form__row" id="draft-attachments">
                            <?= $this->render('_report-attachments', [
                                'attachments' => $model->reportAttachments,
                            ])?>
                        </div>
                        <div class="form__row">
                            <div class="file-upload file-upload--report dropzone step__final--notouch"
                                 data-upload-url="<?= Url::to(['/report/dropzone.report'])?>"
                                 data-delete-url="<?= Url::to(['/report/dropzone.remove'])?>"
                                 data-input-name="<?= Html::getInputName($model, 'pictures') ?>"
                            >
                                <div class="dz-message">
                                    <?= SVG::icon(SVG::ICON_IMAGES, ['class' => 'icon filter__icon'])?>
                                    <p class="step__final--hidden"><?= Yii::t('report', 'create.image.browse'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="form__row field-reportform-pictures required<?= $model->hasErrors('pictures') ? ' has-error' : null?>">
                            <div class="help-block"><?= Html::error($model, 'pictures') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-4 step__help">
                    <div class="panel panel--info panel--title-offset">
                        <div class="panel__body">
                            <p><?= Yii::t('report', 'create.image.upload.title'); ?></p>
                            <p><?= Yii::t('report', 'create.image.upload.description') ?></p>

                            <div class="hidden--desktop offset--top">
                                <p><?= Yii::t('report', 'create.video.upload.title'); ?></p>
                                <p><?= Yii::t('report', 'create.video.upload.description'); ?></p>
                                <p>
                                    <?= Html::img($bundle->baseUrl . '/images/vimeo-logo-color.png', ['alt' => 'Vimeo', 'class' => 'middle']) ?>
                                    <?= Html::img($bundle->baseUrl . '/images/yt-logo-color.png', ['alt' => 'Youtube', 'class' => 'middle']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="form__group form__asset form__asset--borderless">
                        <div class="step__final--hidden">
                            <p>
                                <span class="form__title--1">
                                    <span class="form__legend_icon step__hidden">
                                        <?= SVG::icon(SVG::ICON_VIDEO_CAMERA, ['class' => 'icon filter__icon'])?>
                                    </span>
                                </span>

                                <span class=""><?= Yii::t('report', 'create.video.share') ?></span>
                            </p>
                            <div id="video-link" style="display: none;">
                                <?= $form->field($model, 'videos[]')->hiddenInput([
                                    'data-url' => Url::to(['report/video-embed']),
                                    'data-preloader' => Yii::getAlias('@web/preloader.gif'),
                                ])->label(false) ?>
                            </div>
                        </div>
                        <div class="video-container step__final--notouch"></div>
                    </div>

                    <div class="form__row hidden--desktop">
                        <button type="button" class="button button--large button--submit" show-step="4"><?= Yii::t('report', 'step.next') ?></button>
                    </div>
                </div>
                <div id="video-link-panel" class="col-xs-12 col-lg-4 step__help" style="display: none;">
                    <div class="panel panel--info panel--title-offset panel--label-offset">
                        <div class="panel__body">
                            <p><?= Yii::t('report', 'create.video.upload.title'); ?></p>
                            <p><?= Yii::t('report', 'create.video.upload.description'); ?></p>
                            <p>
                                <?= Html::img($bundle->baseUrl . '/images/vimeo-logo-color.png', ['alt' => 'Vimeo', 'class' => 'middle']) ?>
                                <?= Html::img($bundle->baseUrl . '/images/yt-logo-color.png', ['alt' => 'Youtube', 'class' => 'middle']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="step" step="4">
            <div class="row">
                <div class="col-xs-12 col-lg-8">
                    <div class="form-group form__asset">
                        <div class="form__title">
                            <span class="form__legend_icon step__hidden">
                                <?= SVG::icon(SVG::ICON_CIRCLE_USER, ['class' => 'icon filter__icon'])?>
                            </span>
                            <?= Yii::t('label', 'report.user'); ?>
                            <span class="form__legend_icon step__helper step__visible">
                                <?= SVG::icon(SVG::ICON_WHAT, ['class' => 'icon filter__icon'])?>
                            </span>
                            <span class="form__legend_icon step__edit step__partial--hidden hidden--desktop" show-step="4">
                                <?= SVG::icon(SVG::ICON_PEN, ['class' => 'icon filter__icon'])?>
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-lg-6">
                                <?= $form->field($model, 'nameLast')->textInput([
                                    'disabled' => !Yii::$app->user->isGuest,
                                    'class' => 'input input--default step__final--hidden',
                                ]) ?>
                                <input type="text" class="step__final" readonly sync="#reportform-namelast">
                            </div>
                            <div class="col-xs-12 col-lg-6">
                                <?= $form->field($model, 'nameFirst')->textInput([
                                    'disabled' => !Yii::$app->user->isGuest,
                                    'class' => 'input input--default step__final--hidden',
                                ]) ?>
                                <input type="text" class="step__final" readonly sync="#reportform-namefirst">
                            </div>
                            <div class="col-xs-12">
                                <?= $form->field($model, 'email', [
                                    'errorOptions' => ['class' => 'help-block', 'encode' => false],
                                ])->textInput([
                                    'disabled' => !Yii::$app->user->isGuest,
                                    'class' => 'input input--default step__final--hidden',
                                ]) ?>
                                <input type="text" class="step__final" readonly sync="#reportform-email">
                            </div>
                            <div class="col-xs-12 step__final--notouch">
                                <div class="checkbox--container">
                                    <?= $form->field($model, 'anonymous')->checkbox(['checkbox-label' => Yii::t('report', 'dont_show_my_name')]) ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form__row hidden--desktop">
                        <button type="button" class="button button--large button--submit" show-step="final"><?= Yii::t('report', 'step.next') ?></button>
                    </div>

                    <?php if (Yii::$app->user->getIsGuest()):
                        $text_policy = Yii::t('report', 'create.policy', ['link' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_TOS])]); ?>
                        <div class="form__row">
                            <?= $form->field(
                                $model,
                                'privacyPolicy',
                                [
                                    'template' => '<label for="reportform-privacypolicy" class="checkbox--label checkbox--wrap">{input}<div class="top checkbox--wrap" style="margin-top: 10px;"><div>'
                                        . $text_policy . '</div></div></label><br><br>{error}',
                                ]
                            )
                                ->checkbox(['checkbox-css' => 'top checkbox--left']);
                            ?>
                        </div>
                    <?php endif ?>

                    <div class="step__partial--hidden">
                        <div class="form__row">
                            <?= Html::submitButton(Yii::t('button', 'save_report'), ['class' => 'button disable-after-submit button--primary button--solid button--full--tablet button--loader', 'name' => 'save']) ?>
                            <?= Html::submitButton(Yii::t('button', 'draft_report'), ['class' => 'button button--link button--large button--full--tablet button--loader', 'name' => 'draft']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-4 step__help">
                    <div class="panel panel--info panel--title-offset panel--label-offset">
                        <div class="panel__body">
                            <p><?= Yii::t('report', 'create.nameinfo.title'); ?></p>
                            <p><?= Yii::t('report', 'create.nameinfo.description') ?></p>
                            <br>
                            <p><?= Yii::t('report', 'create.nameinfo.anonim'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
<?php ActiveForm::end();

$this->registerJs(
    '$(document).ready(function() {
            Button.disableAfterSubmit("#report-create-form");
        });'
);