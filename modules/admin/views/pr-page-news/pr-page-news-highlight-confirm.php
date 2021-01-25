<?php

use yii\bootstrap\Html;

/* @var \app\models\db\PrPageNews $model */
/* @var \app\models\db\PrPageNews $highlightedModel */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('pr_page_news', 'confirm.highlight.title') ?></h4>
    </div>

    <div class="modal-body">
        <?php if ($model->isHighlighted()): ?>
            <p><?= Yii::t('pr_page_news', 'confirm.undo_highlight.confirm.first_part') ?>
                <?= $model->title ?>
                <?= Yii::t('pr_page_news', 'confirm.undo_highlight.confirm.second_part') ?></p>
        <?php else: ?>
            <p><?= Yii::t('pr_page_news', 'confirm.highlight.confirm.first_part') ?>
                <?= $model->title ?>
                <?= Yii::t('pr_page_news', 'confirm.highlight.confirm.second_part') ?></p>

            <?php if ($highlightedModel): ?>
                <p><?= Yii::t('pr_page_news', 'confirm.highlight.message.first_part') ?>
                    <?= $highlightedModel->title ?>
                    <?= Yii::t('pr_page_news', 'confirm.highlight.message.second_part') ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?= Html::a(Yii::t('button', 'cancel'), '#', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-label' => Yii::t('label', 'generic.close')]) ?>&nbsp;
        <?= Html::a(Yii::t('pr_page_news', 'button.save_changes'), ['pr-page-news/highlight', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-method' => 'post']) ?>
    </div>
</div>
