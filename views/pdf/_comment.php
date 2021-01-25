<?php
/* @var \app\models\db\ReportActivity[] $comments */
?>

<h3><?=mb_strtoupper(Yii::t('report', 'report.pdf.comments.title')) ?></h3>

<?php if (empty($comments)): ?>
    <?=Yii::t('report', 'report.pdf.comments.empty')?>
<?php return;
endif; ?>

<div class="datasheet">
    <table width="100%" cellpadding="0" cellspacing="2" border="0">
        <tr>
            <td width="33%"><b><?= Yii::t('report', 'report.pdf.comments.header.date') ?></b></td>
            <td width="34%"><b><?= Yii::t('report', 'report.pdf.comments.header.owner') ?></b></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td valign="top">
                    <b><?= date(Yii::t('report', 'report.pdf.comments.dateformat'), $comment->created_at) ?></b>
                </td>
                <td valign="top">
                    <b><?= $comment->getOwnerName() ?></b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: justify"><?= nl2br($comment->comment) ?></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<br />
