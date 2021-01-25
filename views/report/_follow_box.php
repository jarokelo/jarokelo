<?php

use app\components\widgets\Pjax;

use app\components\ActiveForm;
use app\components\helpers\Html;

use app\assets\AppAsset;

$bundle = AppAsset::register($this);

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */

if (!Yii::$app->user->isGuest && !$model->isMyReport()):
    Pjax::begin([
        'id' => 'report-follow-box',
        'formSelector' => '#follow-report-form',
        'linkSelector' => false,
        'clientOptions' => ['cache' => false],
    ]); ?>
    <div class="row">
        <div class="report__follow__container col-xs-12 col-lg-7">
            <div id="report-follow-box" class="">
                <section class="report__follow">
                    <h3 class="report__section-title"><?= Yii::t('label', 'generic.follow'); ?></h3>
                    <p class="report__follow__lead"><?= Yii::t('report', 'follow.description'); ?></p>
                </section>
            </div>
        </div>
        <div class="col-xs-12 col-lg-5">
            <?php
            ActiveForm::begin([
                'id' => 'follow-report-form',
                'action' => ['report/follow', 'id' => $model->id],
                'options' => [
                    'data-pjax' => '1',
                ],
            ]);

            echo Html::submitButton($model->isFollowing() ? Yii::t('report', 'follow-end') : Yii::t('report', 'follow'), ['class' => 'report__follow__button button button--large button--success']);

            ActiveForm::end(); ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
    <?php
endif;
