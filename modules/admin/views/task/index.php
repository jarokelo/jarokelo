<?php

use app\modules\admin\controllers\TaskController;
use yii\widgets\ListView;
use yii\bootstrap\Tabs;

/* @var \yii\web\View $this */
/* @var \yii\data\ActiveDataProvider $activeReports */
/* @var \yii\data\ActiveDataProvider $newReports */
/* @var \yii\data\ActiveDataProvider $comments */
/* @var string $tab */

if (!in_array($tab, [TaskController::TAB_ACTIVE, TaskController::TAB_NEW, TaskController::TAB_COMMENT])) {
    $tab = TaskController::TAB_ACTIVE;
}

$this->title = Yii::t('menu', 'task');

?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('task', 'tab.active') . " ({$activeReports->getTotalCount()})",
            'content' => $tab == TaskController::TAB_ACTIVE ? $this->render('_active-report-activities', [
                'activeReports' => $activeReports,
            ]) : '',
            'active' => $tab == TaskController::TAB_ACTIVE,
            'url' => ['task/index', 'tab' => 'active'],
        ],
        [
            'label' => Yii::t('task', 'tab.new') . " ({$newReports->getTotalCount()})",
            'content' => $tab == TaskController::TAB_NEW ? ListView::widget([
                'dataProvider' => $newReports,
                'itemView' => '/report/_report_block',
                'viewParams' => [
                    'displayDataArray' => TaskController::commentData(),
                ],
                'summary' => false,
                'layout' => "{items}\n<div class=\"text-center\">{pager}</div>",
                'emptyText' => Yii::t('task', 'no-task-found'),
            ]) : '',
            'active' => $tab == TaskController::TAB_NEW,
            'url' => ['task/index', 'tab' => 'new'],
        ],
        [
            'label' => Yii::t('task', 'tab.comment') . " ({$comments->getTotalCount()})",
            'content' => $tab == TaskController::TAB_COMMENT ? $this->render('_comments', [
                'comments' => $comments,
            ]) : '',
            'active' => $tab == TaskController::TAB_COMMENT,
            'url' => ['task/index', 'tab' => 'comment'],
        ],
    ],
])?>
    <!-- Assign Modal -->
    <div class="modal fade" id="task-assign-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="task-assign-modal-body"></div>
    </div>
<?php

\yii\helpers\Html::hiddenInput('tab', $tab);
