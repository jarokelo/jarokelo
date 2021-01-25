<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $model */

?>

<td class="col-md-6" colspan="2">
    <img src="<?= $model->getIncomingEmailPictureUrl() ?>" class="profile_picture" style="float:left;margin-right: 10px;" />
    <div>
        <?php if ($model->institution_id === null || $model->institution === null): ?>
            <?= Yii::t('task', 'activity.incoming_email_plain', ['email' => $model->email->from]) ?>
        <?php else: ?>
            <?= Yii::t('task', 'activity.incoming_email', ['institution' => $model->institution->name]) ?>
        <?php endif ?>
    </div>
    <div><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:mm d. H:i') ?></div>
</td>
<td class="col-md-6" colspan="2">
    <div>
        <?= Html::a(Yii::t('task', 'index.assign'), '#', ['class' => 'btn btn-primary btn-modal-content assign-report', 'data-modal' => '#task-assign-modal', 'data-url' => Url::to(['task/assign', 'id' => $model->id]), 'data-target' => '#task-assign-modal-body']) ?>
        <?= Html::a(Yii::t('report', 'report.comment.hide'), Url::to(['task/approve', 'id' => $model->id, 'redirectTab' => \app\modules\admin\controllers\TaskController::TAB_ACTIVE]), ['class' => 'approve-report btn btn-warning']) ?>
    </div>
</td>

<?php
$this->registerJs(
    '$(\'#task-assign-modal\').on(\'shown.bs.modal\', function() {
        $(document).off(\'focusin.modal\');
    });'
);
