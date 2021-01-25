<?php

use app\models\db\Admin;

use kartik\select2\Select2;
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
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('street', 'create'),
                '#',
                [
                    'class' => 'btn-modal-content btn btn-primary pull-right',
                    'data-modal' => '#street-add-modal',
                    'data-url' => Url::to(['city/street', 'id' => $city->id]),
                    'data-target' => '#street-add-modal-body',
                ]
            ) ?>
            <br><br>
        <?php } ?>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'street-grid-view-search',
            'enableClientValidation' => false,
            'action' => ['city/streets', 'id' => $city->id],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'class' => 'change-pjax-submit',
                'data-pjax-selector' => '#city-streets-search',
            ],
        ]) ?>

        <div class="col-md-6">
            <?= $form->field($searchModel, 'name')->textInput([
                'autocomplete' => 'off',
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($searchModel, 'district')->widget(Select2::className(), [
                'data' => ['' => Yii::t('street', 'search.all_districts')] + $searchModel->getAvailableDistricts(),
                'theme' => Select2::THEME_KRAJEE,
            ]) ?>
        </div>

        <?php ActiveForm::end() ?>
    </div>
</div>

<?php Pjax::begin([
    'id' => 'city-streets',
    'formSelector' => '#street-create-ajax',
    'linkSelector' => '.table .btn-modal-content',
    'options' => [
        'class' => 'pjax-hide-modal',
        'data-modal' => '#street-add-modal',
        'data-pjax-target' => 'street-create-ajax',
    ],
]) ?>

<?php Pjax::begin([
    'id' => 'city-streets-search',
    'formSelector' => '#street-grid-view-search',
    'options' => [
        'data-pjax-target' => 'street-grid-view-search',
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
                            'data-url' => Url::to(['city/street', 'id' => $city->id, 'sid' => $model->id]),
                            'data-target' => '#street-add-modal-body',
                            'data-modal' => '#street-add-modal',
                            'data-pjax' => '0',
                        ]);
                    }
                },
            ],
            [
                'attribute' => 'district.name',
                'label' => Yii::t('street', 'grid.district'),
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\Street $model */
                    if ($model->district !== null) {
                        return $model->district->name;
                    }

                    return '';
                },
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) use ($city) {
                        /* @var \app\models\db\Street $model */

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
                                'data-url' => Url::to(['city/delete-street', 'id' => $city->id, 'sid' => $model->id]),
                                'data-target' => '#street-delete-modal-body',
                                'data-modal' => '#street-delete-modal',
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

<?php Pjax::end() ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="street-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="street-add-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="street-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="street-delete-modal-body"></div>
    </div>
<?php endif ?>
</div>
