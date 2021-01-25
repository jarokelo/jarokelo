<?php

/** @var array $ranks */
/** @var array $ranksMonth */
/** @var \app\models\db\User $user */

?>
<?= $this->render('_hero'); ?>

<div class="container statistics">
    <?= $this->render('_menu'); ?>
    <div class="row text-center">
        <div class="col-xs-12 col-md-6 statistics__user__wrapper">
            <h2><?php echo Yii::t('statistics', 'toplist.monthly_toplist') ?></h2>
            <p>
                <a href="#" class="link link--info"><?= Yii::t('statistics', 'toplist.how_it_works'); ?></a>
            </p>
            <br>
            <?= $this->render('_toplist', [
                'ranks' => $ranksMonth,
                'inCurrentMonth' => true,
            ]) ?>
        </div>

        <div class="col-xs-12 col-md-6 statistics__user__wrapper">
            <h2><?php echo Yii::t('statistics', 'toplist.global_toplist') ?></h2>
            <p>
                <a href="#" class="link link--info"><?= Yii::t('statistics', 'toplist.how_it_works'); ?></a>
            </p>
            <br>
            <?= $this->render('_toplist', [
                'ranks' => $ranks,
                'inCurrentMonth' => false,
            ]) ?>
        </div>
    </div>
</div>
<br>

<?= $this->render('@app/views/_snippets/_hero-bottom-dual');
