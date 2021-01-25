<?php

use app\components\helpers\SVG;

/* @var $reportUniqueName string */
/* @var $infoLink string */
/* @var $buttonLink string */
?>

<div class="institution-info__wrapper">
    <div class="institution-info">
        <div class="institution-info__media">
            <?= SVG::icon(SVG::ICON_FLAG, ['class' => 'icon institution-info__icon']) ?>
        </div>
        <div class="institution-info__text">
            <p><?= Yii::t('report', 'institution.flash.info.header') ?></p>
            <p><?= Yii::t('report', 'institution.flash.info.body', ['reportUniqueName' => $reportUniqueName, 'link' => $infoLink]) ?></p>
            <?= Yii::t('report', 'institution.flash.info.button', ['link' => $buttonLink]) ?>
        </div>
    </div>
</div>
