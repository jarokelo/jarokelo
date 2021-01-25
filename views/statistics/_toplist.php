<?php
/** @var $inCurrentMonth boolean */
use app\components\helpers\Html;
use app\components\helpers\Link;
use app\models\db\User;

$in = false;
/** @var User $user */
foreach ($ranks as $user) {
    if (!Yii::$app->user->isGuest && $user->id == Yii::$app->user->id) {
        $in = true;
    }
    ?>
    <div class="statistics__user statistics__user--<?= $user['rank']; ?> statistics__user--all clearfix <?php echo $in && $user->id == Yii::$app->user->id ? 'statistics__user--loggedin' : ''; ?>">
        <div class="rank"><p class="rank__inside"><?= $user['rank']; ?></p></div>
        <div class="image"><div class="image__wrapper"><img src="<?= User::getPictureUrl($user); ?>" alt="" /></div></div>
        <p class="name"><?= Html::a($user->getFullName(), Link::to([Link::PROFILES, $user->id]), ['class' => 'link link--black']); ?></p>
        <div class="point"><p class="points"><?= $user['points']; ?> <?= Yii::t('statistics', 'toplist.points'); ?></p></div>
    </div>
    <?php
}

/* if the user is not in the toplist */
if (!$in && !Yii::$app->user->isGuest):
    $user = Yii::$app->user->getIdentity();
    ?>
    <div class="text-center">. . .</div>
    <div class="statistics__user statistics__user--loggedin clearfix">
        <div class="rank"><p class="rank__inside"><?= $user->getRank($inCurrentMonth); ?></p></div>
        <div class="image"><div class="image__wrapper"><img src="<?= User::getPictureUrl($user); ?>" alt="" /></div></div>
        <p class="name"><?= Html::a($user->getFullName(), Link::to([Link::PROFILES, $user->id]), ['class' => 'link link--black']); ?></p>
        <div class="point"><p class="points"><?= $user->getPoints($inCurrentMonth); ?> <?= Yii::t('statistics', 'toplist.points'); ?></p></div>
    </div>
    <?php
endif;
?>
