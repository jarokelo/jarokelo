<?php
use yii\helpers\Html;
use app\components\helpers\Link;

/**
 * @var \yii\web\View $this
 * @var string $userIdHash
 */
?>

<div class="form container--box--desktop">
    <div class="flex center-xs">
        <div class="col-xs-12 col-lg-5 col--off text-left">
            <?= Html::a(
                Yii::t('auth', 'reset-button'),
                // explicitly appended token hash as query string
                // because base Yii url encoding (via Link::to) breaks token
                Link::to(
                    sprintf(
                        '/auth/confirm-token-reset?data=%s',
                        $userIdHash
                    )
                ),
                ['style' => 'color: black']
            ) ?>
        </div>
    </div>
</div>
