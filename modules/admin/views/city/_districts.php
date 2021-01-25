<?php

use app\models\db\Admin;

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\models\db\City $city */
/* @var \yii\data\ActiveDataProvider $dataProvider */

?>
<div class="table">
<?php if ($dataProvider === null) { ?>
    <?= Yii::t('city', 'update.no_districts') ?>
<?php } else { ?>
    <div class="row">
        <div class="col-md-12">
            <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT) && $city->has_districts) { ?>
                <?= Html::a(
                    Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                    Yii::t('district', 'create'),
                    '#',
                    [
                        'class' => 'btn-modal-content btn btn-primary pull-right',
                        'data-modal' => '#district-add-modal',
                        'data-url' => Url::to(['city/district', 'id' => $city->id]),
                        'data-target' => '#district-add-modal-body',
                    ]
                ) ?>
                <br><br>
            <?php } ?>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'city-districts',
        'formSelector' => '#district-create-ajax',
        'linkSelector' => '.table .btn-modal-content',
        'options' => [
            'class' => 'pjax-hide-modal',
            'data-modal' => '#district-add-modal',
        ],
    ]) ?>

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
                                'data-url' => Url::to(['city/district', 'id' => $city->id, 'did' => $model->id]),
                                'data-target' => '#district-add-modal-body',
                                'data-modal' => '#district-add-modal',
                                'data-pjax' => '0',
                            ]);
                        }
                    },
                ],
                [
                    'attribute' => 'streetCount',
                    'label' => Yii::t('city', 'grid.streetCount'),
                ],
                [
                    'class' => 'app\components\ActionColumn',
                    'template' => '{delete}',
                    'contentOptions' => ['align' => 'right'],
                    'buttons' => [
                        'delete' => function ($url, $model) use ($city) {
                            /* @var \app\models\db\District $model */

                            if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
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
                                    'data-url' => Url::to([
                                        'city/delete-district',
                                        'id' => $city->id,
                                        'did' => $model->id,
                                    ]),
                                    'data-target' => '#district-delete-modal-body',
                                    'data-modal' => '#district-delete-modal',
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

    <?php Pjax::end() ?>

    <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT) && $city->has_districts) { ?>
        <!-- Modal -->
        <div class="modal fade" id="district-add-modal" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" id="district-add-modal-body"></div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT) && $city->has_districts) { ?>
        <!-- Modal -->
        <div class="modal fade" id="district-delete-modal" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" id="district-delete-modal-body"></div>
        </div>
    <?php } ?>
<?php } ?>
</div>
