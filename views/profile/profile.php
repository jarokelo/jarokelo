<?php
use app\components\helpers\Link;
use app\components\helpers\SVG;
use app\assets\AppAsset;
use yii\helpers\Html;

/** @var \app\models\db\User $user */

$bundle = AppAsset::register($this);

echo $this->render('_profile_top', [
    'user' => $user,
    'view' => $view,
]); ?>
<div class="container profile">

    <div class="filter filter--subpage">
        <div>
            <?= $this->render('_profile_search', [
                'model' => $searchModel,
                'view' => $view,
            ]); ?>
        </div>
    </div>

    <?php if (count($dataProvider->getModels()) > 0): ?>
        <ul class="list list--cards row">
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
            <?= $this->render('@app/views/report/_card', [
                'report' => $model,
            ]); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <?= $this->render('@app/views/_snippets/_no-reports-found.php', ['link' => Link::to(Link::REPORTS)]) ?>
    <?php endif; ?>


    <div class="pagination">
    <?= \app\components\LinkPager::widget([
        'pagination' => $dataProvider->pagination,
    ]); ?>
    </div>
</div>
