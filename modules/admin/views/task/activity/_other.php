<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $model */

?>

<td class="col-md-6" colspan="2">
    <img src="<?= $model->getPictureUrl() ?>" class="profile_picture" style="float:left;margin-right: 10px;" />
    <div class="message"><?= $message ?></div>
    <div class="fs-medium fc-grey"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:mm d. H:i') ?></div>
</td>
<td class="col-md-5">
    <div><span class="label label-primary"><?= Yii::t('const', 'report.status.' . $model->report->status) ?></span></div>
    <div>
        <?= Html::a($model->report->name, ['reports/view', 'id' => $model->report_id], ['class' => 'view-report']) ?>
    </div>
    <div>
        <span class="fs-small"><?= $model->report->getUniqueName() ?></span>
    </div>
</td>
<td class="col-md-1">
    <?= Html::a(Yii::t('report', 'report.comment.approve'), Url::to(['task/approve', 'id' => $model->id, 'redirectTab' => \app\modules\admin\controllers\TaskController::TAB_ACTIVE]), [
        'class' => 'btn btn-sm btn-success approve-report',
    ]) ?>
</td>
