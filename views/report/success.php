<?php

use yii\helpers\Url;
use app\components\helpers\Link;

$this->title = Yii::t('menu', 'report');

/** @var string $scenario */

if ('default' == $scenario): ?>
    <div class="report form container--box">
        <div class="report__success">
            <div class="row center-xs">
                <div class="col-xs-12 col-lg-5">
                    <section class="section section--grey section--rounded">
                        <div class="row center-xs">
                            <div class="col-xs-10">
                                <div class="form__container">
                                    <div class="form__row">
                                        <div class="icon--seal">
                                            <div class="icon--checkmark"></div>
                                        </div>
                                    </div>
                                    <div class="form__row">
                                        <h1 class="heading--2"><?= Yii::t('report', 'report.success.header.default') ?></h1>
                                    </div>
                                    <div class="form__row">
                                        <p><?= Yii::t('report', 'report.success.para2.default2', ['url' => Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]), 'linkClass' => 'link link--info']) ?></p>
                                        <p><strong><?= Yii::t('report', 'report.success.sub_header.default') ?></strong></p>
                                        <p><?= Yii::t('report', 'report.success.para3.default2') ?></p>
                                    </div>
                                    <div class="form__spacer"></div>
                                    <div class="support-box__button text-center">
                                        <a class="button button--large button--primary" href="<?=Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_SUPPORT])?>"><?=Yii::t('app', 'hero.support')?></a>                </div>
                                    <p><?= Yii::t('report', 'report.success.back_to_index', ['url' => Url::to(['index']), 'linkClass' => 'link link--info']) ?></p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="report form container--box">
        <div class="report__success">
            <div class="row center-xs">
                <div class="col-xs-12 col-lg-5">
                    <section class="section section--grey section--rounded">
                        <div class="row center-xs">
                            <div class="col-xs-10">
                                <div class="form__container">
                                    <div class="form__row">
                                        <div class="icon--seal">
                                            <div class="icon--checkmark"></div>
                                        </div>
                                    </div>
                                    <div class="form__row">
                                        <h1 class="heading--2"><?= Yii::t('report', 'report.success.header.' . $scenario) ?></h1>
                                        <p><?= Yii::t('report', 'report.success.para1.' . $scenario) ?></p>
                                    </div>
                                    <div class="form__row">
                                        <p><strong><?= Yii::t('report', 'report.success.sub_header.' . $scenario) ?></strong></p>
                                        <p><?= Yii::t('report', 'report.success.para2.' . $scenario, ['url' => $scenario == 'draft' ? Link::to(Link::PROFILE_DRAFTS) : Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]), 'linkClass' => 'link link--info']) ?></p>
                                    </div>
                                    <div class="form__spacer"></div>
                                    <p><?= Yii::t('report', 'report.success.back_to_index', ['url' => Url::to(['index']), 'linkClass' => 'link link--info']) ?></p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
