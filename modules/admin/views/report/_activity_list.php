<?php
use app\models\db\ReportActivity;
use app\components\widgets\Pjax;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */

Pjax::begin([
    'id' => 'activity-list',
])
?>

<?php $typeDisplayData = ReportActivity::typeDisplayData() ?>

<?php foreach ($model->reportActivities as $activity): ?>
    <?php if (!isset($typeDisplayData[$activity->type])) {
        continue;
    } ?>
    <?= $this->render('_activity_block', [
        'model' => $activity,
        'report' => $model,
        'displayData' => $typeDisplayData[$activity->type],
    ]) ?>
<?php endforeach ?>

<?php

Pjax::end();
