<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

$this->title = $name;
?>

<aside class="hero hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="hero__background hero--404"></div>
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-10 col-sm-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('error', 'page-title')?></h2>
                    <p class="hero__lead">
                        <?= Yii::t('error', 'first-line')?></br>
                        <?= Yii::t('error', 'second-line')?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
