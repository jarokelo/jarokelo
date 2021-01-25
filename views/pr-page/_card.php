<?php
use yii\helpers\Html;
use app\components\helpers\Link;
use \app\assets\AppAsset;
use \app\models\db\PrPageNews;

$bundle = AppAsset::register($this);

/* @var \app\models\db\PrPageNews $item */
/* @var \app\models\db\PrPage $model */
?>

<article class="card news-card">
    <div class="row">
        <div class="col-custom-1">
            <figure class="<?php if ($item->image_file_name) { ?>
                card__media
            <?php } ?>">
                <?php if ($item->isHighlighted()) { ?>
                    <div class="badge--comment--top-right">
                        <span class="badge custom-background-color" style="--color: <?= $model->custom_color ?>;"><?= Yii::t('pr_page_news', 'highlighted') ?></span>
                    </div>
                <?php } ?>
                <?php if ($item->image_file_name) { ?>
                    <img src="<?= PrPageNews::getImageUrl($item) ?>" alt="">
                <?php } ?>
            </figure>
        </div>
    </div>
    <div style="
            padding-top:<?php if (!$item->image_file_name) { ?>
                1.15em; <?php } else { ?>
                0;
            <?php } ?>">
        <span  class="card__label" >
            <time  datetime="<?= Yii::$app->formatter->asDatetime($item->published_at, 'php:c') ?>"><?= Yii::$app->formatter->asDate($item->published_at); ?></time>
        </span>
    </div>
    <h2 class="heading" style="
            margin-top: <?php if (!$item->image_file_name && $item->isHighlighted()) { ?>
                0.5em 0;<?php } else { ?>
                0;
            <?php } ?>;
            color: <?= $model->custom_color ?>;"><?= $item->title ?>
    </h2>
    <div class="news-card__text">
        <?= $item->renderText() ?>
    </div>
    <div class="news-card__button">
        <a class="ajax-news-modal init-loader news-card__button-link" data-url="<?= Link::to([Link::PR_PAGE, $model->slug, 'hirek', $item->id]) ?>" style="--color: <?= $model->custom_color ?>; cursor: pointer;">
            <?= Yii::t('pr_page_news', 'button.more') ?>
        </a>
    </div>
</article>