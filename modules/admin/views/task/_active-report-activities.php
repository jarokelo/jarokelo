<?php
/** @var \yii\data\ActiveDataProvider $activeReports */

use yii\widgets\ListView;
use yii\widgets\LinkPager;
use app\modules\admin\controllers\TaskController;
?>

<table class="table table-white">
    <?php if ($activeReports->getTotalCount() > 0): ?>
        <thead>
            <tr class="row">
                <td class="col-md-6" colspan="2"><?= Yii::t('task', 'index.activity') ?></td>
                <td class="col-md-5"><?= Yii::t('task', 'index.report') ?></td>
                <td class="col-md-1">&nbsp;</td>
            </tr>
        </thead>
    <?php endif; ?>

    <tbody>
        <?= ListView::widget([
            'dataProvider' => $activeReports,
            'itemView' => '_activity_block',
            'emptyText' => Yii::t('task', 'no-task-found'),
            'viewParams' => [
                'activityDataArray' => TaskController::taskActivityData(),
            ],
            'summary' => false,
            'layout' => '{items}',
        ]); ?>
    </tbody>
</table>

<div class="text-center">
    <?= LinkPager::widget([
        'pagination' => $activeReports->pagination,
    ]) ?>
</div>
