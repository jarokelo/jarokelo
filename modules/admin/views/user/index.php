<?php

use app\models\db\Admin;
use app\models\db\User;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\modules\admin\models\UserSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('menu', 'user');

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
    </div>
</div>

<div class="row block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'user-grid-view-search',
        'enableClientValidation' => false,
        'action' => ['user/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'change-pjax-submit',
            'data-pjax-selector' => '#user-grid',
        ],
    ]) ?>

    <div class="col-md-6">
        <?= $form->field($searchModel, 'name_or_email')->textInput([
            'autocomplete' => 'off',
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($searchModel, 'status')->widget(Select2::className(), [
            'data' => ['' => Yii::t('data', 'user.search.all_statuses')] + User::statuses(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<div class="row table">
    <?php Pjax::begin([
        'id' => 'user-grid',
        'formSelector' => '#user-grid-view-search',
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
                'value' => function ($user) {
                    /* @var \app\models\db\User $user */
                    return Html::tag('div', '', [
                        'class' => 'status',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => ($user->status == User::STATUS_ACTIVE) ? 'Aktív felhasználó' : 'Törölt felhasználó',
                        'style' => 'background-color: ' . ($user->status == User::STATUS_ACTIVE ? 'green' : 'red') . ';',
                    ]);
                },
            ],
            [
                'attribute' => 'fullName',
                'label' => Yii::t('user', 'index.full_name'),
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_EDIT)) {
                        return $model->fullName;
                    } else {
                        return Html::a(
                            $model->fullName,
                            Url::to(['user/update', 'id' => $model->id]),
                            ['data-pjax' => 0]
                        );
                    }
                },
            ],
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'reportCount',
                'label' => Yii::t('user', 'index.reports'),
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{kill} {restore} {list} {delete} {full-data-export}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        /* @var \app\models\db\User $model */

                        if ($model->status == $model::STATUS_INACTIVE) {
                            return '';
                        }

                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_DELETE)) {
                            return '';
                        }

                        return Html::a(
                            Html::tag(
                                'span',
                                '',
                                [
                                    'class' => 'btn-primary btn-danger btn-sm glyphicon glyphicon-trash',
                                ]
                            ),
                            $url,
                            [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to(['user/delete', 'id' => $model->id]),
                                'data-target' => '#user-delete-modal-body',
                                'data-modal' => '#user-delete-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'restore' => function ($url, $model) {
                        /* @var \app\models\db\User $model */

                        if ($model->status != $model::STATUS_INACTIVE) {
                            return '';
                        }

                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_DELETE)) {
                            return '';
                        }

                        return Html::a(
                            Html::tag(
                                'span',
                                '',
                                [
                                    'class' => 'btn-primary btn-sm glyphicon glyphicon-repeat',
                                ]
                            ),
                            Url::to(
                                [
                                    'user/restore',
                                    'id' => $model->id,
                                ]
                            ),
                            [
                                'title' => Yii::t('yii', 'Restore'),
                                'aria-label' => Yii::t('yii', 'Restore'),
                            ]
                        );
                    },
                    'list' => function ($url) {
                        return Html::a(
                            Html::tag(
                                'span',
                                '',
                                [
                                    'class' => 'btn-primary btn-sm glyphicon glyphicon-list',
                                ]
                            ),
                            $url,
                            [
                                'title' => Yii::t('user', 'index.reports'),
                                'aria-label' => Yii::t('user', 'index.reports'),
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'kill' => function ($url, $model) {
                        /* @var \app\models\db\User $model */
                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_KILL)) {
                            return '';
                        }

                        return Html::a(
                            Html::tag(
                                'span',
                                '',
                                [
                                    'class' => 'btn-primary btn-danger btn-sm glyphicon glyphicon-screenshot',
                                ]
                            ),
                            $url,
                            [
                                'title' => Yii::t('yii', 'Kill'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'KILL'),
                                'data-url' => Url::to(['user/kill', 'id' => $model->id]),
                                'data-target' => '#user-kill-modal-body',
                                'data-modal' => '#user-kill-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'full-data-export' => function ($url, $model) {
                        /* @var \app\models\db\User $model */
                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_USER_FULL_DATA_EXPORT)) {
                            return '';
                        }

                        return Html::a(
                            '',
                            [
                                'user/full-data-export',
                                'id' => $model->id,
                            ],
                            [
                                'class' => 'btn btn-primary glyphicon glyphicon-download-alt',
                                'target' => '_blank',
                            ]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    /* @var \app\models\db\User $model */

                    switch ($action) {
                        case 'update':
                            return ['user/update', 'id' => $model->id];

                        case 'list':
                            return ['report/user', 'id' => $model->id];

                        default:
                            return '#';
                    }
                },
            ],
        ],
    ]) ?>

    <?php Pjax::end() ?>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_USER_DELETE)): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="user-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="user-delete-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_USER_KILL)): ?>
    <!-- Kill Modal -->
    <div class="modal fade" id="user-kill-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="user-kill-modal-body"></div>
    </div>
<?php endif ?>
