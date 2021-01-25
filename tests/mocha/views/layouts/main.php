<?php
use app\tests\mocha\assets\MochaAsset;
use app\tests\mocha\assets\ChaiAsset;
use app\tests\mocha\assets\MochaSinonAsset;
use app\tests\mocha\assets\SinonChaiAsset;
use app\tests\mocha\assets\LecheAsset;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
MochaAsset::register($this);
ChaiAsset::register($this);
SinonChaiAsset::register($this);
MochaSinonAsset::register($this);
LecheAsset::register($this);

$this->registerJs("
    if (typeof initMochaPhantomJS === 'function') {
        initMochaPhantomJS();
    }
    mocha.setup('bdd');
", \yii\web\View::POS_BEGIN);

$this->registerJs("
    mocha.checkLeaks();
    mocha.globals(['jQuery']);
    mocha.run();
", \yii\web\View::POS_END);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Mocha Tests</title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div id="mocha"></div>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
