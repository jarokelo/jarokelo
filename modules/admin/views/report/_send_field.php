<?php

use app\modules\admin\models\ExtraContact;

use yii\bootstrap\Html;

/* @var \yii\web\View $this */

$timestamp = number_format(microtime(true) * 1000, 0, null, '');
$model = new ExtraContact();
$model->test = isset($test) ? $test : 0;

?>

<div class="extra-contact">
    <div class="row">
        <div class="col-md-5">
            <?= Html::activeInput('text', $model, '[' . $timestamp . ']name', [
                'class' => 'form-control',
                'placeholder' => Yii::t('report', 'send.extra_contact.name'),
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= Html::activeInput('text', $model, '[' . $timestamp . ']email', [
                'class' => 'form-control',
                'placeholder' => Yii::t('report', 'send.extra_contact.email'),
            ]) ?>
        </div>
        <div class="col-md-1">
            <?= Html::activeHiddenInput($model, '[' . $timestamp . ']test') ?>
            <?= Html::a(Html::tag('span', Html::tag('span', '', ['class' => 'btn-danger btn-sm glyphicon glyphicon-trash'])), '#', ['class' => 'remove-extra-contact']) ?>
        </div>
    </div>
</div>
