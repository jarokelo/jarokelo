<?php

    use app\components\helpers\SVG;
    use yii\helpers\Url;

    $socialIconClass = 'icon list__icon footer__social-list__icon';

?>
<ul class="list list--icons footer__social-list">
    <li class="footer__social-list__item">
        <a class="list__link link" href="//www.facebook.com/myprojecthu" target="_blank">
            <?= SVG::icon(SVG::ICON_FACEBOOK, ['class' => $socialIconClass]) ?>
            <span class="footer__social-list__title"><?= Yii::t('label', 'footer.social.facebook')?></span>
        </a>
    </li>
    <li class="footer__social-list__item">
        <a class="list__link link" href="//www.instagram.com/myproject/" target="_blank">
            <?= SVG::icon(SVG::ICON_INSTAGRAM, ['class' => $socialIconClass]) ?>
            <span class="footer__social-list__title"><?= Yii::t('label', 'footer.social.instagram')?></span>
        </a>
    </li>
    <li class="footer__social-list__item">
        <a class="list__link link" href="//twitter.com/myprojecthu" target="_blank">
            <?= SVG::icon(SVG::ICON_TWITTER, ['class' => $socialIconClass]) ?>
            <span class="footer__social-list__title"><?= Yii::t('label', 'footer.social.twitter')?></span>
        </a>
    </li>
    <li class="footer__social-list__item">
        <a class="list__link link" href="http://myproject.blog.hu/" target="_blank">
            <?= SVG::icon(SVG::ICON_BLOG, ['class' => $socialIconClass]) ?>
            <span class="footer__social-list__title"><?= Yii::t('label', 'footer.social.blog')?></span>
        </a>
    </li>
    <li class="footer__social-list__item">
        <a class="list__link link" href="<?= Url::to(['/rss/index']); ?>">
            <?= SVG::icon(SVG::ICON_RSS, ['class' => $socialIconClass]) ?>
            <span class="footer__social-list__title"><?= Yii::t('label', 'footer.social.rss')?></span>
        </a>
    </li>
</ul>
