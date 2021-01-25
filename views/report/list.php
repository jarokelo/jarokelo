<?php

/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var \app\models\db\search\ReportSearch $searchModel */


use \app\assets\AppAsset;
use app\components\helpers\Link;
use app\components\widgets\Pjax;
use app\components\LinkPager;

$bundle = AppAsset::register($this);

Pjax::begin([
    'id' => 'report-list-container',
    'enablePushState' => true,
    'enableReplaceState' => false, // with PJAX enabled it should be FALSE otherwise overriding PUSH state
    'formSelector' => '#report-search-form',
    'scrollTo' => 0,
]); ?>

<div class="container">
    <?= $this->render('_search', [
        'model' => $searchModel,
        'type' => Link::REPORTS,
    ]); ?>
</div>

<?php if (count($dataProvider->getModels()) > 0): ?>
    <div class="container" id="report-list-container">
        <ul class="list list--cards row">
            <?php foreach ($dataProvider->getModels() as $model): ?>
                <li class="flex-eq-height col-xs-12 col-md-6 col-lg-3">
                    <?= $this->render('_card', [
                        'report' => $model,
                    ]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="pagination">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'linkOptions' => [
                'data-pjax' => 1,
            ],
        ]); ?>
    </div>
<?php else: ?>
    <?= $this->render('@app/views/_snippets/_no-reports-found.php', ['link' => Link::to(Link::REPORTS)]) ?>
<?php endif;

Pjax::end() ?>

<?= $this->render('/_snippets/_hero-bottom-dual') ?>
