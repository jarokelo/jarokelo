<?php

use app\models\db\Admin;
use app\models\db\ReportActivity;
use app\components\helpers\Html;
use yii\helpers\Url;
use app\models\db\ReportAttachment;
use app\components\ActiveForm;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $model */
/* @var \app\models\db\Report $report */
/* @var array $displayData */

if (!isset($displayData) && !empty($displayDataArray)) {
    $displayData = $displayDataArray[$model->type];
}

$pictureShown = isset($displayData['picture']) && $displayData['picture'] !== ReportActivity::PICTURE_NONE;
$messageData = isset($displayData['message']) ? $displayData['message'] : null;

$message = '';

if ($messageData !== null && isset($messageData['category']) && isset($messageData['key'])) {
    /*$message = Yii::t(
        $messageData['category'],
        $messageData['key'],
        $model->calculateParameters(false)
    );*/
    $message = Yii::t(
        $messageData['category'],
        $messageData['key'],
        ReportActivity::resolveParameters(false, $model, $messageData)
    );
}

echo $this->render('_image_editor');
?>

<div class="row">
    <?php if ($pictureShown): ?>
        <div class="col-md-1 text-center">
            <?= Html::img(Url::to($model->getPictureUrl()), ['class' => 'profile_picture']) ?>
        </div>
        <div class="col-md-11">
    <?php else: ?>
        <div class="col-md-12">
    <?php endif; ?>
        <div><?= $message ?></div>
        <div class="fs-small"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></div>

        <?php if (isset($displayData['show_comment']) && $displayData['show_comment'] === true): ?>
            <div class="comment">
                <?php if (!empty($model->getCommentContent())) { ?>
                    <div class="comment__container">
                        <div class="comment__text">
                            <?= Html::formatText($model->getCommentContent()) ?>
                        </div>
                        <div class="comment__more"><span class="glyphicon glyphicon-menu-down"></span><?= Yii::t('report', 'show_more') ?> </div>
                        <div class="comment__less"><span class="glyphicon glyphicon-menu-up"></span><?= Yii::t('report', 'show_less') ?></div>
                    </div>
                <?php } ?>

                <div class="comment__attachment gallery">
                <?php
                foreach ($model->reportAttachments as $activityAttachment) {
                    $attachmentUrl = $activityAttachment->getAttachmentUrl(\app\models\db\ReportAttachment::SIZE_PICTURE_THUMBNAIL);
                    $origAttachmentUrl = $activityAttachment->getAttachmentUrl();

                    if ($activityAttachment->type == ReportAttachment::TYPE_COMMENT_PICTURE) {
                        echo Html::a(
                            Html::img($attachmentUrl, ['style' => 'margin-bottom: 10px;']),
                            $origAttachmentUrl,
                            [
                                'class' => 'lightbox',
                                'style' => 'display: block',
                                'data-pjax' => 0,
                            ]
                        );
                        echo Html::a(
                            Html::tag('span', '', [
                                'class' => 'glyphicon glyphicon-edit',
                                'style' => 'margin-right: 0px;',
                            ]),
                            [
                                'report/edit-image',
                                'id' => $activityAttachment->id,
                            ],
                            [
                                'class' => 'btn-modal-content btn btn-default edit-img pull-left',
                                'title' => Yii::t('report', 'update.edit.image'),
                                'data-url' => Url::to(['edit-image', 'id' => $activityAttachment->id]),
                                'data-target' => '#image-editor-modal-body',
                                'data-modal' => '#image-editor-modal',
                                'style' => 'display: inline-block; width: 40px; margin-top: 5px;',
                            ]
                        );

                        $form = ActiveForm::begin([
                            'action' => [
                                'report/delete-comment',
                                'id' => $activityAttachment->id,
                                'reportId' => $report->id,
                            ],
                            'options' => [
                                'class' => 'delete-attachment',
                            ],
                        ]);?>
                        <?= Html::hiddenInput(Html::getInputName($model, 'attachments[]'), $activityAttachment->name) ?>

                        <button style="margin: 5px 0 0 5px;" title="<?=Yii::t('report', 'image.delete')?>" class="inline-block btn btn-default">
                            <span style="margin: 0;" class="glyphicon glyphicon-trash"></span>
                        </button>

                        <?php $form::end();
                    } else {
                        echo '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>' . Html::a($activityAttachment->name, $attachmentUrl, ['target' => '_blank', 'data-pjax' => 0]);
                    }

                    echo '</br>';
                }
                ?>
                </div>

                <?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)): ?>
                    <div class="comment__edit">
                        <?php if (isset($displayData['edit']) && $displayData['edit'] === true): ?>
                            <?= Html::a(
                                Yii::t('report', 'update.edit'),
                                [
                                    'report/edit-comment',
                                    'id' => $model->id,
                                ],
                                [
                                    'class' => 'btn-modal-content btn btn-default btn-sm',
                                    'data-modal' => '#edit-comment-modal-' . $model->id,
                                    'data-url' => Url::to(['report/edit-comment', 'id' => $model->id]),
                                    'data-target' => '#edit-comment-modal-body-' . $model->id,
                                ]
                            ) ?>
                        <?php endif; ?>
                        <?php if (isset($displayData['hide']) && $displayData['hide'] === true): ?>
                            <?= Html::a(Yii::t('report', $model->visible === 1 ? 'report.comment.hide' : 'report.comment.show'), '#', [
                                'class' => 'btn-toggle-comment btn btn-default btn-sm',
                                'data-url' => Url::to(['report/toggle-comment', 'id' => $model->id]),
                            ]) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)): ?>
    <!-- Edit Comment Modal -->
    <div class="modal fade" id="edit-comment-modal-<?php echo $model->id; ?>" role="dialog" aria-labelledby="Edit Comment" aria-hidden="true">
        <div class="modal-dialog" id="edit-comment-modal-body-<?php echo $model->id; ?>"></div>
    </div>
<?php endif;

$this->registerJs('deleteAttachment("' . Yii::t('report', 'image.delete.message') . '")'); ?>

<script>
    function deleteAttachment(text) {
        $('.delete-attachment').on('beforeSubmit', function () {
            if (!confirm(text)) {
                return false;
            }

            return true;
        });
    }
</script>
