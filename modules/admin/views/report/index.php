<?php

use yii\bootstrap\Html;
use app\models\db\Admin;

/* @var \yii\web\View $this */
/* @var \app\modules\admin\models\ReportSearch $searchModel */
/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('menu', 'report');

?>

    <div class="row">
        <div class="col-md-9 col-sm-12"><h2><?= Html::encode($this->title) ?></h2></div>
        <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_STATISTICS)) { ?>
        <div class="col-md-3 col-sm-12">
            <?= Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-stats']) .
                Yii::t('menu', 'statistics'),
                ['report/statistics'],
                ['class' => 'pull-right btn btn-default btn-info']
            ) ?>
        </div>
        <?php } ?>
    </div>

<?= $this->render('_report_list', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
    'action' => ['reports/index'],
]) ?>