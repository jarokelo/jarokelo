<?php

use app\components\helpers\Link;
use yii\helpers\Html;
use app\components\ActiveForm;
use app\models\db\Report;

$dropDownWrapperOptions = ['class' => 'select select--primary'];

/* @var $this yii\web\View */
/* @var $model app\models\db\search\ReportSearch */
/* @var $form app\components\ActiveForm */

$view = isset($view) && $view == true;
?>

<div class="report-search-filter">
    <?php $form = ActiveForm::begin([
        'action' => !$view ? Link::to(Link::PROFILE) : null,
        'id' => 'form-profile-filter',
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'status')->dropDownList(($view ? Report::adminFilteredStatuses() : Report::statuses()), ['class' => '', 'prompt' => Yii::t('label', 'generic.all')], $dropDownWrapperOptions)->label(''); ?>
    <div class="filter__title filter__title--after filter__title--before filter__title--inline"><?php echo Yii::t('profile', $view ? 'reports' : 'my_reports'); ?></div>
    <?= Html::submitButton(Yii::t('label', 'generic.submit'), ['class' => 'btn btn-primary', 'style' => 'display:none;']) ?>
    <?php ActiveForm::end(); ?>
</div>
