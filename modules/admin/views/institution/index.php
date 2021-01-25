<?php

use app\models\db\Admin;
use app\models\db\City;
use app\models\db\Institution;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\modules\admin\models\InstitutionSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('menu', 'institution');

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_ADD)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('institution', 'create'),
                '#',
                [
                    'class' => 'pull-right btn btn-primary btn-modal-content',
                    'data-modal' => '#institution-add-modal',
                    'data-url' => Url::to(['institution/create']),
                    'data-target' => '#institution-add-modal-body',
                ]
            ) ?>
        <?php } ?>
    </div>
</div>

<div class="row block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'institution-grid-view-search',
        'enableClientValidation' => false,
        'action' => ['institution/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'change-pjax-submit',
            'data-pjax-selector' => '#institution-grid',
        ],
    ]) ?>

    <div class="col-md-6">
        <?= $form->field($searchModel, 'name_or_email')->textInput([
            'autocomplete' => 'off',
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($searchModel, 'city')->widget(Select2::className(), [
            'data' => ['' => Yii::t('institution', 'index.all_cities')] + City::availableCities(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

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
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
                        return $model->name;
                    } else {
                        return Html::a(
                            $model->name,
                            Url::to(
                                [
                                    'institution/update',
                                    'id' => $model->id,
                                ]
                            ),
                            ['data-pjax' => 0]
                        );
                    }
                },
            ],
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'city.name',
                'label' => Yii::t('data', 'institution.city_id'),
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\Institution $model */
                    $types = Institution::types();
                    if (isset($types[$model->type])) {
                        return $types[$model->type];
                    }

                    return Yii::t('institution', 'type.unknown');
                },
            ],
            [
                'attribute' => 'reportCount',
                'label' => Yii::t('city', 'grid.reportCount'),
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{list} {prPageUpdate} {delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        /* @var \app\models\db\Institution $model */

                        if (!Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN) {
                            return '';
                        }

                        return Html::a(
                            '<span class="btn-primary btn-danger btn-sm glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to(['institution/delete', 'id' => $model->id]),
                                'data-target' => '#institution-delete-modal-body',
                                'data-modal' => '#institution-delete-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'list' => function ($url) {
                        return Html::a('<span class="btn-primary btn-sm glyphicon glyphicon-list"></span>', $url, [
                            'title' => Yii::t('institution', 'index.reports'),
                            'aria-label' => Yii::t('institution', 'index.reports'),
                            'data-pjax' => '0',
                        ]);
                    },
                    'prPageUpdate' => function ($url, $model) {
                        /* @var \app\models\db\Institution $model */

                        if ($model->prPage == null && Yii::$app->user->identity->status == Admin::STATUS_SUPER_ADMIN) {
                            return Html::a('<span class="btn-primary btn-sm glyphicon glyphicon-plus"></span>', Url::to(['pr-page/create', 'id' => $model->id]), [
                                'title' => Yii::t('institution', 'index.pr_page.new'),
                                'aria-label' => Yii::t('institution', 'index.pr_page.new'),
                                'data-pjax' => '0',
                            ]);
                        }
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    /* @var \app\models\db\Institution $model */

                    switch ($action) {
                        case 'update':
                            return ['institution/update', 'id' => $model->id];

                        case 'list':
                            return ['report/institution', 'id' => $model->id];

                        default:
                            return '#';
                    }
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_ADD)): ?>
    <!-- Modal -->
    <div class="modal fade" id="institution-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="institution-add-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_DELETE)): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="institution-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="institution-delete-modal-body"></div>
    </div>
<?php endif ?>

