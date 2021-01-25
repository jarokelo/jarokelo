<?php

use app\models\db\Admin;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var int $id */
/* @var \yii\data\ActiveDataProvider $dataProvider */

?>
<div class="block--grey">
    <div class="row">
        <div class="col-md-6">
            <h3><?= Yii::t('institution', 'update.contacts') ?></h3>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('institution', 'contact.create'),
                '#',
                [
                    'class' => 'pull-right btn btn-primary btn-modal-content',
                    'data-modal' => '#contact-add-modal',
                    'data-target' => '#contact-add-modal-body',
                    'data-url' => Url::to(['institution/contact', 'id' => $id]),
                ]
            ) ?>
        </div>
    </div>
    <div class="row table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summaryOptions' => ['class' => 'summary pull-right'],
            'summary' => Yii::t('admin', 'grid.summary'),
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model, $key, $index) {
                        if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
                            return '';
                        }

                        return Html::a(
                            $model->name,
                            '#',
                            [
                                'title' => Yii::t('yii', 'Update'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-url' => Url::to([
                                    'institution/contact',
                                    'id' => $model->institution_id,
                                    'cid' => $model->id,
                                ]),
                                'data-target' => '#contact-add-modal-body',
                                'data-modal' => '#contact-add-modal',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
                [
                    'attribute' => 'email',
                ],
                [
                    'class' => 'app\components\ActionColumn',
                    'template' => '{delete}',
                    'contentOptions' => ['align' => 'right'],
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            /* @var \app\models\db\Contact $model */

                            if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)) {
                                return '';
                            }

                            return Html::a('<span class="btn-primary btn-sm glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'btn-modal-content',
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-url' => Url::to([
                                    'institution/delete-contact',
                                    'id' => $model->institution_id,
                                    'cid' => $model->id,
                                ]),
                                'data-target' => '#contact-delete-modal-body',
                                'data-modal' => '#contact-delete-modal',
                                'data-pjax' => '0',
                            ]);
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
</div>
