<?php

use app\models\db\ReportActivity;

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var ReportActivity $model */
/* @var array $activityData */

if (!isset($activityData) && !empty($activityDataArray)) {
    $activityData = $activityDataArray[$model->type];
}

$message = '';

$messageData = isset($activityData['message']) ? $activityData['message'] : null;
if ($messageData !== null && isset($messageData['category']) && isset($messageData['key'])) {
    $message = Yii::t($messageData['category'], $messageData['key'], ReportActivity::resolveParameters(true, $model, $messageData));
}

?>

<tr class="row tr-activity">
    <?php if (isset($activityData['custom']) && $activityData['custom'] === true): ?>
        <?= $this->render('activity/_' . $model->type, [
            'model' => $model,
        ]) ?>
    <?php else: ?>
        <?= $this->render('activity/_other', [
            'model' => $model,
            'message' => $message,
        ]) ?>
    <?php endif ?>
</tr>
