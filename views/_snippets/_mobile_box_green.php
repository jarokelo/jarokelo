<?php
$bundle = \app\assets\AppAsset::register($this);
?><aside class="application-box">
    <div class="container clearfix">
        <img class="application-box__media" src="<?= $bundle->baseUrl; ?>/images/mobile-application.png" alt="">

        <div class="application-box__content col-7">
            <h2 class="heading heading--3 application-box__title"><?= Yii::t('app', 'appbox.title'); ?></h2>
            <p class="application-box__lead">
                <?= Yii::$app->params['mobile']['enabled'] ? Yii::t('app', 'appbox.content1') : Yii::t('app', 'appbox.content1.disabled') ?><br>
                <?= Yii::t('app', 'appbox.content2'); ?>
            </p>
            <?php if (Yii::$app->params['mobile']['enabled']): ?>
            <a target="_blank" href="<?= Yii::$app->params['mobile']['links']['ios'] ?>" class="application-box__icon button button--app-store ir"><?= Yii::t('app', 'appbox.appstore'); ?></a>
            <a target="_blank" href="<?= Yii::$app->params['mobile']['links']['android'] ?>" class="application-box__icon button button--google-play ir"><?= Yii::t('app', 'appbox.googleplay'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</aside>
