<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \app\models\db\User $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('user', 'kill') ?></h4>
    </div>

    <div class="modal-body">
        <p>
            <?= Yii::t('user', 'kill.message') ?>
        </p>
        <table>
            <tr>
                <td><?= Yii::t('admin', 'label.link_to_report') ?></td>
                <td><?= Yii::t('admin', 'label.comments_count') ?></td>
            </tr>
            <?php foreach ($model->reports as $report): ?>
                <tr>
                    <td><?= Html::a($report->id, Url::to(['report/view', 'id' => $report->id])) ?></td>
                    <td><?= count($report->reportActivities) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('button', 'KILL'), ['user/kill', 'id' => $model->id], ['class' => 'btn btn-danger btn-primary btn-pjax-kill-confirm', 'data-pjax-container' => '#user-grid', 'data-modal' => '#user-kill-modal', 'data' => ['method' => 'post']]) ?>
    </div>
</div>
