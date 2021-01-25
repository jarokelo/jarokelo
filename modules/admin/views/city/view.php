<?php

use app\models\db\Admin;
use app\modules\admin\controllers\CityController;
use app\modules\admin\models\StreetSearch;

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\City $model */
/* @var string $tab */
/* @var \yii\data\BaseDataProvider $districtProvider */
/* @var \yii\data\BaseDataProvider $streetProvider */
/* @var \yii\data\BaseDataProvider $streetGroupProvider */
/* @var \yii\data\BaseDataProvider $ruleProvider */

$this->title = $model->name;
$this->params['breadcrumbs'] = [$this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['city/index'], 'label' => Yii::t('menu', 'city')];

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)) { ?>
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-edit']) .
                Yii::t('city', 'update'),
                ['city/update', 'id' => $model->id],
                [
                    'class' => 'pull-right btn btn-primary btn-modal-content',
                    'data-modal' => '#city-update-modal',
                    'data-url' => Url::to(['city/update', 'id' => $model->id]),
                    'data-target' => '#city-update-modal-body',
                ]
            ) ?>
        <?php } ?>
    </div>
</div>

<div class="row">
    <?= \yii\bootstrap\Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('city', 'update.tab.rules'),
                'content' => $tab == CityController::TAB_RULES ? $this->render('_rules', [
                    'city' => $model,
                    'dataProvider' => $ruleProvider,
                ]) : null,
                'active' => $tab == CityController::TAB_RULES,
                'url' => ['city/view', 'id' => $model->id, 'tab' => CityController::TAB_RULES],
            ],
            [
                'label' => Yii::t('city', 'update.tab.streets'),
                'content' => $tab == CityController::TAB_STREETS ? $this->render('_streets', [
                    'city' => $model,
                    'dataProvider' => $streetProvider,
                    'searchModel' => new StreetSearch(['city' => $model]),
                ]) : null,
                'active' => $tab == CityController::TAB_STREETS,
                'url' => ['city/view', 'id' => $model->id, 'tab' => CityController::TAB_STREETS],
            ],
            [
                'label' => Yii::t('city', 'update.tab.streetgroups'),
                'content' => $tab == CityController::TAB_STREETGROUPS ? $this->render('_streetgroups', [
                    'city' => $model,
                    'dataProvider' => $streetGroupProvider,
                    'searchModel' => new StreetSearch(['city' => $model]),
                ]) : null,
                'active' => $tab == CityController::TAB_STREETGROUPS,
                'url' => ['city/view', 'id' => $model->id, 'tab' => CityController::TAB_STREETGROUPS],
            ],
            [
                'label' => Yii::t('city', 'update.tab.districts'),
                'content' => $tab == CityController::TAB_DISTRICTS ? $this->render('_districts', [
                    'city' => $model,
                    'dataProvider' => $districtProvider,
                ]) : null,
                'active' => $tab == CityController::TAB_DISTRICTS,
                'url' => ['city/view', 'id' => $model->id, 'tab' => CityController::TAB_DISTRICTS],
            ],
        ],
    ])?>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_CITY_EDIT)): ?>
    <!-- Modal -->
    <div class="modal fade" id="city-update-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="city-update-modal-body"></div>
    </div>
<?php endif ?>
