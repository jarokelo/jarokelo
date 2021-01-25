<?php
use app\modules\admin\controllers\TaskController;
use yii\widgets\ListView;

\app\components\widgets\Pjax::begin([
    'id' => 'activity-list',
    'linkSelector' => false,
]);

echo ListView::widget([
    'options' => [
        'class' => 'list-view report__activity',
    ],
    'dataProvider' => $comments,
    'itemView' => 'activity/_comment',
    'emptyText' => Yii::t('task', 'no-task-found'),
    'viewParams' => [
        'displayDataArray' => TaskController::commentData(),
    ],
    'summary' => false,
    'layout' => "{items}\n<div class=\"text-center\">{pager}</div>",
]);

\app\components\widgets\Pjax::end();
