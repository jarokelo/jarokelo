<?php

use yii\bootstrap\Html;

/* @var \yii\web\View $this */
/* @var \app\models\db\Institution $institution */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('report', 'title.by', ['name' => $institution->name]);
$this->params['breadcrumbs'] = [['label' => $institution->name, 'url' => ['institution/update', 'id' => $institution->id]], Yii::t('menu', 'report')];
$this->params['breadcrumbs_homeLink'] = ['url' => ['institution/index'], 'label' => Yii::t('menu', 'institution')];

?>

<div class="row">
    <div class="col-md-9"><h2><?= Html::encode($this->title) ?></h2></div>
    <div class="col-md-3">
        <div class="pull-right">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="glyphicon glyphicon-save"></span>
                <span><?= Yii::t('report', 'export.list') ?> </span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><?= Html::a(Yii::t('report', 'export.excel'), ['report/institution-export', 'id' => $institution->id, 'type' => \app\models\db\Report::SOURCE_EXCEL]) ?></li>
                <li><?= Html::a(Yii::t('report', 'export.pdf'), ['report/institution-export', 'id' => $institution->id, 'type' => \app\models\db\Report::SOURCE_PDF]) ?></li>
            </ul>
        </div>
    </div>
</div>

<?= $this->render('_report_list', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
    'action' => ['report/institution', 'id' => $institution->id],
    'disableInstitution' => true,
]) ?>
