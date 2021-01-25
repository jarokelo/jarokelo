<?php

use app\models\db\Admin;
use app\models\db\City;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\components\widgets\Pjax;

/* @var \yii\web\View $this */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \app\modules\admin\models\CitySearch $searchModel */
/* @var array $count */

$this->title = Yii::t('menu', 'city');

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_ADD)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) .
                Yii::t('city', 'create'),
                ['city/create'],
                [
                    'class' => 'pull-right btn btn-primary btn-modal-content',
                    'data-modal' => '#city-add-modal',
                    'data-url' => Url::to(['city/create']),
                    'data-target' => '#city-add-modal-body',
                ]
            ) ?>
        <?php } ?>
    </div>
</div>

<div class="row block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'city-grid-view-search',
        'enableClientValidation' => false,
        'action' => ['city/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'change-pjax-submit',
            'data-pjax-selector' => '#city-grid',
        ],
    ]) ?>

    <div class="col-md-6">
        <?= $form->field($searchModel, 'name')->textInput([
            'autocomplete' => 'off',
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($searchModel, 'status')->widget(Select2::className(), [
            'data' => ['' => Yii::t('city', 'search.all_statuses')] + City::statuses(),
            'theme' => Select2::THEME_KRAJEE,
        ]) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<div class="row table">
    <?php Pjax::begin([
        'id' => 'city-grid',
        'formSelector' => '#city-grid-view-search',
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
                    /* @var \app\models\db\City $model */
                    return Html::tag('div', '', [
                        'class' => 'status',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => ($model->status == City::STATUS_ACTIVE) ? Yii::t('city', 'status.active') : Yii::t('city', 'status.inactive'),
                        'style' => 'background-color: ' . ($model->status == City::STATUS_ACTIVE ? 'green' : 'red') . ';',
                    ]);
                },
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model, $key, $index) {
                    if (!Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) {
                        return $model->name;
                    } else {
                        return Html::a($model->name, Url::to(['city/view', 'id' => $model->id]), ['data-pjax' => 0]);
                    }
                },
            ],
            [
                'attribute' => 'districtCount',
                'label' => Yii::t('city', 'grid.districtCount'),
            ],
            [
                'attribute' => 'streetCount',
                'label' => Yii::t('city', 'grid.streetCount'),
            ],
            [
                'attribute' => 'reportCount',
                'label' => Yii::t('city', 'grid.reportCount'),
                'value' => function (City $model) use ($count) {
                    if (isset($count[$model->id])) {
                        return $count[$model->id];
                    }

                    return 0;
                },
            ],
            [
                'attribute' => 'adminCount',
                'label' => Yii::t('city', 'grid.adminCount'),
            ],
        ],
    ]);
    ?>

    <?php Pjax::end() ?>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_ADD)): ?>
    <!-- Modal -->
    <div class="modal fade" id="city-add-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="city-add-modal-body"></div>
    </div>
<?php endif ?>
