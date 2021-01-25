<?php
/** @var $link string */
    use app\components\helpers\SVG;
    use yii\helpers\Html;

?>
<div class="container">
    <div class="search">
        <div class="row center-xs">
            <div class="col-xs-12 col-md-8 col-lg-5">
                <?= SVG::icon(SVG::ICON_MAGNIFIY, ['class' => 'icon search__icon']) ?>
                <p class="search__text"><?= Yii::t('report', 'no-reports-found-for-this-filters') ?></p>
                <?= Html::a(Yii::t('button', 'erase-search-filters'), $link, ['class' => 'link link--info']) ?>
            </div>
        </div>
    </div>
</div>
