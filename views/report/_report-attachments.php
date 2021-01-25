<?php
/**
 * @var $attachments \app\models\db\ReportAttachment[]
 */
use app\components\helpers\Html;
use app\components\helpers\SVG;
use yii\helpers\Url;

?>
<?php foreach ($attachments as $attachment): ?>
    <?php if ($attachment->isImageAttachment()): ?>
    <div class="report__attachment">
        <div class="report__attachment__img">
            <?= Html::img($attachment->getAttachmentUrl(\app\models\db\ReportAttachment::SIZE_PICTURE_THUMBNAIL), [
                'class' => 'image',
            ])?>
        </div>
        <?= Html::a(SVG::icon(SVG::ICON_CLOSE), '#', [
            'class' => 'report__attachment__btn remove-attachment',
            'data-url' => Url::to(['delete-attachment', 'id' => $attachment->id], true),
        ]) ?>
    </div>
    <?php endif; ?>
<?php endforeach; ?>
