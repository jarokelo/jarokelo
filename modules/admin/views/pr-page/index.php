<?php

use app\models\db\Admin;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\models\db\PrPage $model */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('menu', 'pr_page');

?>

<div class="row table">
    <?php Pjax::begin([
        'id' => 'institution-grid',
        'formSelector' => '#institution-grid-view-search',
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{items}\n<div class=\"text-center\">{pager}</div>",
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
                        return $model->title;
                    } else {
                        return Html::a(
                            $model->title,
                            Url::to(
                                [
                                    'pr-page/update',
                                    'id' => $model->id,
                                ]
                            ),
                            ['data-pjax' => 0]
                        );
                    }
                },
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('pr_page', 'grid.status'),
                'value' => function ($model) {
                    return $model->getStatusFormatted();
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('pr_page', 'grid.created_at'),
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at);
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
