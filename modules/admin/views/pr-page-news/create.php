<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\PrPageNews $model */

$this->title = Yii::t('label', 'pr_page_news.create');

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= Yii::t('pr_page_news', 'create') ?></h4>
    </div>
    <div class="modal-body">
        <?= $this->render('_form', [
            'model' => $model,
            'action' => ['pr-page-news/create'],
        ]); ?>

        <div class="modal-footer">
            <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
            <?= Html::submitButton(Yii::t('button', 'add'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
