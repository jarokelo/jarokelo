<?php
use app\components\helpers\SVG;
use app\models\db\ReportAttachment;
use yii\helpers\Html;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $model */
/* @var \app\models\db\Report $report */
?>

<div class="comment__pictures">
    <?php foreach ($model->reportAttachments as $activityAttachment): ?>
        <?php if ($activityAttachment->isImageAttachment()): ?>
            <?php
            $img = Html::img($activityAttachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_THUMBNAIL), [
                'class' => 'comment__pictures__picture',
            ]);

            echo Html::a($img, $activityAttachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_ORIGINAL), [
                'class' => '',
            ]);
            ?>
        <?php endif; ?>
    <?php endforeach;?>
</div>

<ul class="comment__attachments">
    <?php foreach ($model->reportAttachments as $activityAttachment): ?>
        <?php if (!$activityAttachment->isImageAttachment()): ?>
            <li class="comment__attachment">
                <?= SVG::icon(SVG::ICON_DOWNLOAD, ['class' => 'link__icon icon icon--before']) .
                Html::a($activityAttachment->name, $activityAttachment->getAttachmentUrl(), [
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'class' => 'link link--info',
                ]); ?>
            </li>
        <?php endif; ?>
    <?php endforeach;?>
</ul>
