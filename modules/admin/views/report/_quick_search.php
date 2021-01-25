<?php

use app\models\db\Report;

use app\models\db\ReportAttachment;
use yii\bootstrap\Html;

use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report[] $reports */
?>

<?php if (count($reports) > 0): ?>
    <?php foreach ($reports as $report): ?>
            <div class="row quick-search-row" data-href="<?= Url::to(['reports/view', 'id' => $report->id]) ?>">
                <div class="col-md-3">
                    <?= Html::img($report->pictureUrl(ReportAttachment::SIZE_PICTURE_THUMBNAIL), ['style' => 'width: 60px; height: 60px;']) ?>
                </div>
                <div class="col-md-9">
                    <div>
                        <?= Html::a($report->name, ['reports/view', 'id' => $report->id]) ?>
                        <span class="label label-default label-status label-status-<?=$report->status?>"><?= Yii::t('const', 'report.status.' . $report->status) ?></span>
                    </div>
                    <div class="fs-small truncate">
                        <?= Yii::t('report', 'block.report_time') ?> <?= Yii::$app->formatter->asDatetime($report->created_at) ?><?= Yii::t('report', 'block.reporter') ?> <?= $report->user->getFullname() ?>
                    </div>
                </div>
            </div>
    <?php endforeach ?>
<?php else: ?>
    <div class="quick-search-empty"><?= Yii::t('menu', 'search.no_results') ?></div>
<?php endif ?>
