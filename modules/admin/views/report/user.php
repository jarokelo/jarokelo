<?php

use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\models\db\User $user */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('report', 'title.by', ['name' => $user->getFullName()]);
$this->params['breadcrumbs'] = [['label' => $user->getFullName(), 'url' => ['user/update', 'id' => $user->id]], Yii::t('menu', 'report')];
$this->params['breadcrumbs_homeLink'] = ['url' => ['user/index'], 'label' => Yii::t('menu', 'user')];
?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
    </div>
</div>

<?= $this->render('_report_list', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
    'action' => ['report/user', 'id' => $user->id],
]) ?>

