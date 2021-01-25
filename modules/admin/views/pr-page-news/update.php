<?php

use app\models\db\Admin;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var \app\models\db\PrPageNews $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['url' => ['pr-page/update', 'id' => $model->getPrPageAsArray()['id']], 'label' => $model->getPrPageAsArray()['title']];
$this->params['breadcrumbs'][] = ['url' => ['pr-page-news/index', 'id' => $model->pr_page_id], 'label' => Yii::t('menu', 'pr_page_news')];
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_VIEW) && Yii::$app->user->identity->hasPermission(Admin::PERM_PR_PAGE_EDIT)) {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['pr-page/index'], 'label' => Yii::t('menu', 'pr_page')];
} else {
    $this->params['breadcrumbs_homeLink'] = ['url' => ['institution/index'], 'label' => Yii::t('menu', 'institution')];
}

?>

<div class="row block--grey">
    <?= $this->render('_form', [
        'model' => $model,
        'action' => ['pr-page-news/update', 'id' => $model->id],
    ]); ?>

    <div class="text-right">
        <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>&nbsp;
        <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
