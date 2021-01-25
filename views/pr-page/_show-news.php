<?php

use app\components\widgets\Pjax;
use app\models\db\PrPageNews;
use yii\helpers\Html;
use app\components\helpers\SVG;

/* @var $this yii\web\View */
/* @var \app\models\db\PrPageNews $model */
?>

<?php Pjax::begin([
    'id' => 'show-news-pjax-container-' . $model->id,
    'clientOptions' => ['cache' => false],
]); ?>

<a href="#close-modal" rel="modal:close" class="close">
    <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon'])?>
</a>

<div class="section--news">
    <?php if ($model->image_file_name) {?>
        <div style="<?= Html::encode('background-image: url("' . PrPageNews::getImageUrl($model) . '");')?> background: no-repeat;">
            <img class="cover-image" src="<?= PrPageNews::getImageUrl($model) ?>" alt="">
        </div>
    <?php } ?>
    <div class="section--news__content">
        <h2 class="section--news__title"><?= $model->title ?></h2>
        <span  class="card__label" >
        <time  datetime="<?= Yii::$app->formatter->asDatetime($model->published_at, 'php:c') ?>"><?= Yii::$app->formatter->asDate($model->published_at); ?></time>
    </span>
        <div class="section--news__text">
            <?= $model->text ?>
        </div>
    </div>
</div>

<?php Pjax::end(); ?>