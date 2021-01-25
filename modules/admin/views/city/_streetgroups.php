<?php

use app\models\db\Admin;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\models\db\City $city */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \app\modules\admin\models\StreetSearch $searchModel */

?>

<div class="table">
<div class="row">
    <div class="col-md-12">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
            <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . Yii::t(
                'street',
                'streetgroup.create'
            ), '#', [
                'class' => 'btn-modal-content btn btn-primary pull-right',
                'data-modal' => '#streetgroup-add-modal',
                'data-url' => Url::to(['city/streetgroup', 'cityId' => $city->id]),
                'data-target' => '#streetgroup-add-modal-body',
            ]) ?>
            <br><br>
        <?php endif; ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'streetgroup-grid-view-search',
            'enableClientValidation' => false,
            'action' => ['city/streetgroups', 'id' => $city->id, 'cityId' => $city->id],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'class' => 'change-pjax-submit',
                'data-pjax-selector' => '#city-streetgroups-search',
            ],
        ]); ?>

        <div class="col-md-12">
            <?= $form->field($searchModel, 'name')->textInput([
                'autocomplete' => 'off',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php Pjax::begin([
    'id' => 'city-streetgroups',
    'formSelector' => '#streetgroup-create-ajax',
    'linkSelector' => '.table .btn-modal-content',
    'options' => [
        'class' => 'pjax-hide-modal',
        'data-modal' => '#streetgroup-add-modal',
        'data-pjax-target' => 'streetgroup-create-ajax',
    ],
]);

Pjax::begin([
    'id' => 'city-streetgroups-search',
    'formSelector' => '#streetgroup-grid-view-search',
    'options' => [
        'data-pjax-target' => 'streetgroup-grid-view-search',
    ],
]);
?>

<div class="row">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model, $key, $index) use ($city) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
                        return $model->name;
                    } else {
                        return Html::a($model->name, '#', [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn-modal-content',
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-url' => Url::to(['city/streetgroup', 'id' => $model->id, 'cityId' => $city->id]),
                            'data-target' => '#streetgroup-add-modal-body',
                            'data-modal' => '#streetgroup-add-modal',
                            'data-pjax' => '0',
                        ]);
                    }
                },
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) use ($city) {
                        /* @var \app\models\db\StreetGroup $model */

                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
                            return '';
                        }

                        return Html::a(
                            '<span class="btn-primary btn-danger btn-sm glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to([
                                    'city/delete-streetgroup',
                                    'id' => $model->id,
                                    'cityId' => $city->id,
                                ]),
                                'data-target' => '#streetgroup-delete-modal-body',
                                'data-modal' => '#streetgroup-delete-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
                'urlCreator' => function () {
                    return '#';
                },
            ],
        ],
    ]);
    ?>
</div>

<?php
Pjax::end();
Pjax::end();
?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="streetgroup-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="streetgroup-add-modal-body"></div>
    </div>
<?php endif;

if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="streetgroup-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="streetgroup-delete-modal-body"></div>
    </div>
<?php endif; ?>
</div>
