<?php

use app\models\db\Admin;

use app\models\db\Rule;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \app\models\db\City $city */
/* @var \yii\data\ActiveDataProvider $dataProvider */

?>
<div class="table">
<div class="row">
    <div class="col-md-12">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('rule', 'create'),
                '#',
                [
                    'class' => 'btn-modal-content btn btn-primary pull-right',
                    'data-modal' => '#rule-add-modal',
                    'data-url' => Url::to(['city/rule', 'id' => $city->id]),
                    'data-target' => '#rule-add-modal-body',
                ]
            ) ?>
            <br><br>
        <?php } ?>
    </div>
</div>

<?php Pjax::begin([
    'id' => 'city-rules',
    'formSelector' => '#rule-create-ajax',
    'linkSelector' => '.table .btn-modal-content',
    'options' => [
        'class' => 'pjax-hide-modal',
        'data-modal' => '#rule-add-modal',
    ],
]) ?>

<div class="row">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summaryOptions' => ['class' => 'summary pull-right'],
        'summary' => Yii::t('admin', 'grid.summary'),
        'columns' => [
            [
                'attribute' => 'status',
                'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\Rule $model */
                    return Html::tag('div', '', [
                        'class' => 'status',
                        'style' => 'background-color: ' . ($model->status === Rule::STATUS_ACTIVE ? 'green' : 'red') . ';',
                    ]);
                },
            ],
            [
                'attribute' => 'institution.name',
                'label' => Yii::t('rule', 'grid.institution'),
                'format' => 'raw',
                'value' => function ($model, $key, $index) use ($city) {
                    $emails = Html::tag('small', implode(', ', \yii\helpers\ArrayHelper::getColumn($model->contacts, 'email')));
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
                        return $model->institution->name . '<br>' . $emails;
                    } else {
                        return Html::a($model->institution->name, '#', [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn-modal-content',
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-url' => Url::to(['city/rule', 'id' => $city->id, 'rid' => $model->id]),
                            'data-target' => '#rule-add-modal-body',
                            'data-modal' => '#rule-add-modal',
                            'data-pjax' => '0',
                        ]) . '<br>' . $emails;
                    }
                },
            ],
            [
                'attribute' => 'district.name',
                'label' => Yii::t('street', 'grid.district'),
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\Rule $model */
                    if ($model->district !== null) {
                        return $model->district->name;
                    }

                    return '';
                },
            ],
            [
                'attribute' => 'street_group.name',
                'label' => Yii::t('rule', 'grid.street_group'),
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var \app\models\db\Rule $model */
                    if ($model->streetGroup !== null) {
                        return $model->streetGroup->name;
                    }

                    return '';
                },
            ],
            [
                'attribute' => 'report_category_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return (empty($model->report_category_id) ? '' : $model->reportCategory->name);
                },
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{delete}',
                'contentOptions' => ['align' => 'right'],
                'buttons' => [
                    'delete' => function ($url, $model) use ($city) {
                        /* @var \app\models\db\Rule $model */

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
                                'data-url' => Url::to(['city/delete-rule', 'id' => $city->id, 'rid' => $model->id]),
                                'data-target' => '#rule-delete-modal-body',
                                'data-modal' => '#rule-delete-modal',
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

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="rule-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="rule-add-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="rule-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="rule-delete-modal-body"></div>
    </div>
<?php endif ?>

</div>
