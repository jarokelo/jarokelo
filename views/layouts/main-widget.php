<?php

use app\assets\AppAsset;
use app\components\helpers\Link;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\helpers\SVG;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
$bundle = AppAsset::register($this);
\app\assets\PasswordValidatorAsset::register($this);

$this->beginPage() ?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <link rel="icon" type="image/png" href="<?= Url::base() ?>/favicon.png">
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script>
        var baseUrl = '<?= Yii::$app->urlManager->createAbsoluteUrl(['/']); ?>';
    </script>
</head>
<body class="widget__body">
<?php $this->beginBody() ?>
<div class="widget__header">
    <h1 class="logo logo--default">
        <span class="visuallyhidden">myProject</span>
        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/']); ?>">
            Logo
        </a>
    </h1>
</div>
        <?= $content ?>
<div class="widget__footer">
    <div style="display:table-cell;text-align:left;"><a target="_blank" href="<?= Link::to([Link::REPORTS]); ?>"><?= Yii::t('menu', 'more_reports'); ?></a></div>
    <div style="display:table-cell;text-align:right;"><a target="_blank" href="<?= Link::to([Link::CREATE_REPORT]); ?>"><?= Yii::t('menu', 'new_report'); ?></a></div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
