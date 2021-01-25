<?php

use app\components\widgets\Pjax;
use app\models\db\Admin;
use app\models\db\City;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\AdminSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('menu', 'admin');

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_ADD)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('admin', 'create'),
                ['admin/create'],
                [
                    'class' => 'pull-right btn-modal-content btn btn-primary',
                    'data-modal' => '#admin-add-modal',
                    'data-url' => Url::to(['admin/create']),
                    'data-target' => '#admin-add-modal-body',
                ]
            ) ?>
        <?php } ?>
    </div>
</div>

<div class="row block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'admin-grid-view-search',
        'enableClientValidation' => false,
        'action' => ['admin/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'change-pjax-submit',
            'data-pjax-selector' => '#admin-grid',
        ],
    ]) ?>

    <div class="col-md-4">
        <?= $form->field($searchModel, 'name_or_email')->textInput([
            'autocomplete' => 'off',
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($searchModel, 'city')->widget(Select2::className(), [
            'data' => ['' => Yii::t('data', 'admin.search.all_cities')] + City::availableCities(false, false),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($searchModel, 'status')->widget(Select2::className(), [
            'data' => ['' => Yii::t('data', 'admin.search.all_statuses')] + Admin::statuses(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<div class="row table">
    <?php Pjax::begin([
        'id' => 'admin-grid',
        'formSelector' => '#admin-grid-view-search',
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
                'value' => function ($admin) {
                    $isUserActive = in_array(
                        $admin->status,
                        [
                            Admin::STATUS_ACTIVE,
                            Admin::STATUS_SUPER_ADMIN,
                        ]
                    );
                    return Html::tag('div', '', [
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => $isUserActive ? 'Aktív felhasználó' : 'Törölt felhasználó',
                        'class' => 'status',
                        'style' => 'background-color: ' . ($isUserActive ? 'green' : 'red') . ';',
                    ]);
                },
            ],
            [
                'attribute' => 'fullName',
                'label' => Yii::t('user', 'index.full_name'),
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    $superAdminLabel = $model->status == Admin::STATUS_SUPER_ADMIN ? ' <span class="label label-default">' . Yii::t('admin', 'status.super_admin') . '</span>' : '';
                    if (
                        !Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_EDIT) ||
                        ($model->status == Admin::STATUS_SUPER_ADMIN && Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN)
                    ) {
                        return $model->fullName . $superAdminLabel;
                    } else {
                        return Html::a(
                            $model->fullName . $superAdminLabel,
                            Url::to(['admin/update', 'id' => $model->id]),
                            ['data-pjax' => 0]
                        );
                    }
                },
            ],
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'assignedCities',
                'label' => Yii::t('admin', 'update.city'),
                'contentOptions' => ['width' => '400px'],
            ],
            [
                'attribute' => 'score',
                'label' => Yii::t('admin', 'index.score'),
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {restore} {delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        /* @var \app\models\db\Admin $model */

                        if (
                            !Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_EDIT) ||
                            ($model->status == Admin::STATUS_SUPER_ADMIN && Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN)
                        ) {
                            return '';
                        }

                        return Html::a('<span class="btn-primary btn-sm glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        /* @var \app\models\db\Admin $model */

                        if ($model->status == $model::STATUS_INACTIVE) {
                            return '';
                        }

                        if (
                            !Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_DELETE) || $model->id == Yii::$app->user->id ||
                            ($model->status == Admin::STATUS_SUPER_ADMIN && Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN)
                        ) {
                            return '';
                        }

                        return Html::a(
                            '<span class="btn-primary btn-danger btn-sm glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to(['delete', 'id' => $model->id]),
                                'data-target' => '#admin-delete-modal-body',
                                'data-modal' => '#admin-delete-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'restore' => function ($url, $model) {
                        /* @var \app\models\db\Admin $model */

                        if ($model->status != $model::STATUS_INACTIVE) {
                            return '';
                        }

                        if (
                            !Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_DELETE) || $model->id == Yii::$app->user->id ||
                            ($model->status == Admin::STATUS_SUPER_ADMIN && Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN)
                        ) {
                            return '';
                        }

                        return Html::a(
                            '<span class="btn-primary btn-sm glyphicon glyphicon-repeat"></span>',
                            Url::to(['restore', 'id' => $model->id]),
                            [
                                'title' => Yii::t('yii', 'Restore'),
                                'aria-label' => Yii::t('yii', 'Restore'),
                            ]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    /* @var \app\models\db\Admin $model */

                    switch ($action) {
                        case 'update':
                            return ['admin/update', 'id' => $model->id];

                        default:
                            return '#';
                    }
                },
            ],
        ],
    ]) ?>

    <?php Pjax::end() ?>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_ADD)): ?>
    <!-- Add Modal -->
    <div class="modal fade" id="admin-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="admin-add-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_ADMIN_DELETE)): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="admin-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="admin-delete-modal-body"></div>
    </div>
<?php endif ?>
