<?php

use app\models\db\Admin;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \yii\web\View $this */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \app\models\db\PrPageNews $model */
/* @var \app\models\db\PrPage $prPageModel */

$this->title = $prPageModel->institution->name . Yii::t('pr_page_news', 'news');
$this->params['breadcrumbs'][] = ['url' => ['pr-page/update', 'id' => $prPageModel->id], 'label' => $prPageModel->title];
$this->params['breadcrumbs'][] = Yii::t('menu', 'pr_page_news');

if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_VIEW) && Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['pr-page/index'], 'label' => Yii::t('menu', 'pr_page')];
} else {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['institution/index'], 'label' => Yii::t('menu', 'institution')];
}

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <?= Html::a(
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
            Yii::t('pr_page_news', 'create'),
            ['pr-page-news/create/'],
            [
                'class' => 'pull-right btn btn-primary btn-modal-content',
                'data-modal' => '#news-add-modal',
                'data-url' => Url::to(['pr-page-news/create', 'id' => $id]),
                'data-target' => '#news-add-modal-body',
            ]
        ) ?>
    </div>
</div>

<div class="row table">
    <?php Pjax::begin([
        'id' => 'city-grid',
        'formSelector' => '#news-grid-view-search',
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{items}\n<div class=\"text-center\">{pager}</div>",
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        'columns' => [
            [
                'attribute' => 'status',
                'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\PrPageNews $model */
                    return Html::tag('div', '', [
                        'class' => 'status',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => ($model->status == \app\models\db\PrPageNews::STATUS_ACTIVE) ? Yii::t('pr_page_news', 'status.active') : Yii::t('pr_page_news', 'status.inactive'),
                        'style' => 'background-color: ' . ($model->status == \app\models\db\PrPageNews::STATUS_ACTIVE ? 'green' : 'red') . ';',
                    ]);
                },
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    return Html::a($model->title, Url::to(['pr-page-news/update', 'id' => $model->id]), ['data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'highlighted',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\PrPageNews $model */
                    return Html::tag('div', '', [
                        'class' => ($model->highlighted == \app\models\db\PrPageNews::HIGHLIGHTED_TRUE) ? 'glyphicon glyphicon-pushpin' : '',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => ($model->highlighted == \app\models\db\PrPageNews::HIGHLIGHTED_TRUE) ? Yii::t('pr_page_news', 'highlighted') : null,
                    ]);
                },
            ],
            [
                'attribute' => 'published_at',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    return Yii::$app->formatter->asDatetime($model->published_at, 'Y.MM.dd');
                },
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{highlight} {changeStatus} {delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        /* @var \app\models\db\PrPageNews $model */

                        return Html::a(
                            '<span class="btn-primary btn-danger btn-sm glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to(['pr-page-news/delete', 'id' => $model->id]),
                                'data-target' => '#pr-page-news-delete-modal-body',
                                'data-modal' => '#pr-page-news-delete-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'highlight' => function ($url, $model) {
                        /* @var \app\models\db\PrPageNews $model */
                        if ($model->status != $model::STATUS_INACTIVE) {
                            return Html::a(
                                $model->isHighlighted() ? '<span class="btn-primary btn-danger btn-sm glyphicon glyphicon-pushpin"></span>' : '<span class="btn-primary btn-success btn-sm glyphicon glyphicon-pushpin"></span>',
                                $url,
                                [
                                    'title' => $model->isHighlighted() ? Yii::t('pr_page_news', 'button.undo_highlight') : Yii::t('pr_page_news', 'button.highlight'),
                                    'class' => 'btn-modal-content',
                                    'aria-label' => $model->isHighlighted() ? Yii::t('pr_page_news', 'button.undo_highlight') : Yii::t('pr_page_news', 'button.highlight'),
                                    'data-url' => Url::to(['pr-page-news/highlight', 'id' => $model->id]),
                                    'data-target' => '#pr-page-news-highlight-modal-body',
                                    'data-modal' => '#pr-page-news-highlight-modal',
                                    'data-pjax' => '0',
                                ]
                            );
                        }
                    },

                    'changeStatus' => function ($url, $model) {
                        /* @var \app\models\db\PrPageNews $model */

                        if ($model->status == $model::STATUS_INACTIVE) {
                            return Html::a(
                                '<span class="btn-primary btn-sm glyphicon glyphicon-ok"></span>',
                                Url::to(['activate', 'id' => $model->id]),
                                [
                                    'title' => Yii::t('pr_page_news', 'button.activate'),
                                    'aria-label' => Yii::t('pr_page_news', 'button.activate'),
                                ]
                            );
                        } else {
                            return Html::a(
                                '<span class="btn-primary btn-sm glyphicon glyphicon-remove"></span>',
                                Url::to(['inactivate', 'id' => $model->id]),
                                [
                                    'title' => Yii::t('pr_page_news', 'button.inactivate'),
                                    'aria-label' => Yii::t('pr_page_news', 'button.inactivate'),
                                ]
                            );
                        }
                    },
                ],
            ],
        ],
    ]);
    ?>




    <?php Pjax::end() ?>
</div>

<!-- Modal -->
<div class="modal fade" id="news-add-modal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" id="news-add-modal-body"></div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="pr-page-news-delete-modal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" id="pr-page-news-delete-modal-body"></div>
</div>

<!-- Highlight Modal -->
<div class="modal fade" id="pr-page-news-highlight-modal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" id="pr-page-news-highlight-modal-body"></div>
</div>
