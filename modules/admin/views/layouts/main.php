<?php

/* @var \yii\web\View $this */
/* @var string $content */

use app\components\helpers\SVG;
use app\models\db\Admin;
use app\modules\admin\assets\AdminAsset;
use app\modules\admin\models\ReportSearch;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$bundle = AdminAsset::register($this);
$this->registerJs('var baseUrl = "' . Url::base(true) . '";', \yii\web\View::POS_HEAD);

/** @var Admin $admin */
$admin = Yii::$app->user->identity;

?>
<?php $this->beginContent($this->findViewFile('layout')); ?>
<div class="wrap">
    <?php NavBar::begin([
        'brandLabel' => Html::img($bundle->baseUrl . '/svg/logo.svg', ['style' => 'width: 150px; padding: 3px;']),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-custom navbar-fixed-top',
        ],
    ]) ?>

    <?php if (!Yii::$app->user->isGuest): ?>
        <?php
        $items = [];

        if ($admin->hasPermission(Admin::PERM_REPORT_EDIT) || $admin->hasPermission(Admin::PERM_REPORT_DELETE) || $admin->hasPermission(Admin::PERM_REPORT_STATISTICS)) {
            $items[] = [
                'label' => Yii::t('menu', 'task'),
                'url' => ['task/index'],
                'active' => Yii::$app->controller->id == 'task',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_REPORT_EDIT) || $admin->hasPermission(Admin::PERM_REPORT_DELETE) || $admin->hasPermission(Admin::PERM_REPORT_STATISTICS)) {
            $items[] = [
                'label' => Yii::t('menu', 'report'),
                'url' => ['report/index'],
                'active' => Yii::$app->controller->id == 'report' && Yii::$app->controller->action->id != 'user' && Yii::$app->controller->action->id != 'institution',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_USER_VIEW)) {
            $items[] = [
                'label' => Yii::t('menu', 'user'),
                'url' => ['user/index'],
                'active' => Yii::$app->controller->id == 'user' || (Yii::$app->controller->id == 'report' && Yii::$app->controller->action->id == 'user'),
            ];
        }

        if ($admin->hasPermission(Admin::PERM_ADMIN_VIEW)) {
            $items[] = [
                'label' => Yii::t('menu', 'admin'),
                'url' => ['admin/index'],
                'active' => Yii::$app->controller->id == 'admin',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_INSTITUTION_VIEW)) {
            $items[] = [
                'label' => Yii::t('menu', 'institution'),
                'url' => ['institution/index'],
                'active' => Yii::$app->controller->id == 'institution' || (Yii::$app->controller->id == 'report' && Yii::$app->controller->action->id == 'institution') || Yii::$app->controller->id == 'pr-page' || Yii::$app->controller->id == 'pr-page-news',
            ];
        }

        if (!$admin->hasPermission(Admin::PERM_INSTITUTION_VIEW) && $admin->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
            $items[] = [
                'label' => Yii::t('menu', 'pr_page'),
                'url' => ['pr-page/index'],
                'active' => Yii::$app->controller->id == 'pr-page' || Yii::$app->controller->id == 'pr-page-news',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_CITY_VIEW)) {
            $items[] = [
                'label' => Yii::t('menu', 'city'),
                'url' => ['city/index'],
                'active' => Yii::$app->controller->id == 'city',
            ];
        }

        $isParentActive = false;
        $otherItems = [];

        if ($admin->isSuperAdmin()) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'cron-log'),
                'url' => ['cron-log/index'],
                'active' => $isCronLog = Yii::$app->controller->id == 'cron-log',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_REPORT_CATEGORY_VIEW)) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'report-category'),
                'url' => ['report-category/index'],
                'active' => $isReportCategoryView = Yii::$app->controller->id == 'report-category',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_REPORT_TAXONOMY_VIEW)) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'report-taxonomy'),
                'url' => ['report-taxonomy/index'],
                'active' => $isReportTaxonomyView = Yii::$app->controller->id == 'report-taxonomy',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_PROJECT_CONFIG_VIEW)) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'project-config'),
                'url' => ['project-config/index'],
                'active' => $isProjectConfigView = Yii::$app->controller->id == 'project-config',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_CUSTOM_QUESTION_VIEW)) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'custom-question'),
                'url' => ['custom-question/index'],
                'active' => $isCustomQuestionView = Yii::$app->controller->id == 'custom-question',
            ];
        }

        if ($admin->hasPermission(Admin::PERM_CUSTOM_FORM_VIEW)) {
            $otherItems[] = [
                'label' => Yii::t('menu', 'custom-form'),
                'url' => ['custom-form/index'],
                'active' => $isCustomFormView = Yii::$app->controller->id == 'custom-form',
            ];
        }

        switch (true) {
            case !empty($isCronLog):
            case !empty($isReportCategoryView):
            case !empty($isReportTaxonomyView):
            case !empty($isProjectConfigView):
            case !empty($isCustomQuestionView):
            case !empty($isCustomFormView):
                $isParentActive = true;
                break;
        }

        if ($otherItems) {
            $items[] = [
                'label' => 'Egyéb',
                'url' => '#',
                'active' => $isParentActive,
                'items' => $otherItems,
            ];
        }
        ?>
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'encodeLabels' => false,
            'items' => $items,
        ]) ?>

        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right dropdown-toggle'],
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => '<img src="' . Admin::getPictureUrl(Yii::$app->user->identity) . '" style="width: 20px; height: 20px; border-radius: 100px;" alt="' . $admin->getFullName() . '">',
                    'items' => [
                        '<div class="text-center">' . $admin->getFullName() . '<br /><span class="fs-big fc-white">' . Yii::t('menu', 'score', ['score' => $admin->getScore()]) . '</span></div><br />',
                        [
                            'label' => '<i class="btn-primary btn-sm glyphicon glyphicon-user"></i> ' . Yii::t('menu', 'profile'),
                            'url' => ['admin/profile'],
                        ],
                        [
                            'label' => '<i class="btn-primary btn-sm glyphicon glyphicon-user"></i> ' . Yii::t('menu', 'password'),
                            'url' => ['admin/password'],
                        ],
                        [
                            'label' => '<i class="btn-primary btn-sm glyphicon glyphicon-log-out"></i> ' . Yii::t('menu', 'logout'),
                            'url' => ['auth/logout'],
                            'linkOptions' => ['data-method' => 'post'],
                        ],
                    ],
                ],
            ],
        ]) ?>

        <?php if ($admin->hasPermission(Admin::PERM_REPORT_EDIT) || $admin->hasPermission(Admin::PERM_REPORT_DELETE) || $admin->hasPermission(Admin::PERM_REPORT_STATISTICS)): ?>
            <div class="navbar-form">
                <?php $form = ActiveForm::begin([
                    'id' => 'quick-search',
                    'enableClientValidation' => false,
                    'action' => ['report/search'],
                    'method' => 'get',
                ]) ?>

                <div class="input-group">
                    <?= Html::activeTextInput(new ReportSearch(), 'text', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => Yii::t('menu', 'search.placeholder')]) ?>
                    <span class="input-group-btn"><button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button></span>
                </div>
                <div id="quick-search-dropdown" class="hidden" style="position: absolute; width: 300px; z-index: 9999; background-color: white; border: 1px solid lightgray; border-radius: 4px;" data-url="<?= Url::to(['report/search']) ?>">
                    <div class="quick-search-container">
                    </div>
                    <button type="submit" style="width: 100%" class="quick-search-button"><?= Yii::t('menu', 'search.all_results') ?> <span class="glyphicon glyphicon-arrow-right"></span></button>
                </div>

                <?php ActiveForm::end() ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php NavBar::end() ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => isset($this->params['breadcrumbs_homeLink']) ? $this->params['breadcrumbs_homeLink'] : null,
            'options' => [
                'class' => 'site-breadcrumb',
            ],
        ]) ?>

        <?= \app\components\AlertWidget::showAlerts(); ?>

        <?= $content ?>
    </div>
</div>

<?= $this->render('_hotkeys') ?>

<?php $this->endContent(); ?>
