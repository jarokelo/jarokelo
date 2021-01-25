<?php
use app\assets\HighlightJsAsset;
use yii\web\View;

/* @var yii\web\View $this */
HighlightJsAsset::register($this);

$this->registerJs('hljs.initHighlightingOnLoad();', View::POS_END);

?>
<div class="container apidoc">
    <div class="row">
        <div class="col-xs-12 col-md-2">
            <ul class="toc">
                <li>
                    <a href="#authentication">Authentication</a>
                    <ul>
                        <li><a href="#login">Login</a></li>
                    </ul>
                </li>
                <li><a href="#response">Response</a></li>
                <li><a href="#caching">Caching</a></li>
                <li><a href="#categories">Categories</a></li>
                <li>
                    <a href="#cities">Cities</a>
                    <ul>
                        <li><a href="#cities-index">List cities</a></li>
                        <li><a href="#cities-view">City details</a></li>
                        <li><a href="#cities-streets">Streets</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#reports">Reports</a>
                    <ul>
                        <li><a href="#reports-index">List reports</a></li>
                        <li><a href="#reports-view">Report details</a></li>
                        <li><a href="#submit">Submit report</a></li>
                    </ul>
                </li>
                <li><a href="#comment">Comment</a></li>
            </ul>
        </div>
        <div class="col-xs-12 col-md-10">

            <h1><?= Yii::t('meta', 'title.api') ?></h1>

            <?= $this->render('authentication'); ?>
            <?= $this->render('response'); ?>
            <?= $this->render('caching'); ?>
            <?= $this->render('login'); ?>
            <?= $this->render('categories'); ?>
            <?= $this->render('cities'); ?>
            <?= $this->render('streets'); ?>
            <?= $this->render('reports'); ?>
            <?= $this->render('submit'); ?>
            <?= $this->render('comment'); ?>

        </div>
    </div>
</div>
