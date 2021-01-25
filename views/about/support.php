<?php

use app\assets\AppAsset;
use app\components\helpers\SVG;

/**
 * @var \Yii\Web\View $this
 */

$assetUrl = AppAsset::register($this)->baseUrl;
?>
<section class="donate-box form--padding">
    <div class="container mb">
        <div class="row">
            <div class="col-xs-12">
                <div class="donate-box--transfer form__spacer border">
                    <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-1') ?></h3>
                </div>
                <p><?= Yii::t('support', 'donate-text-1') ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="donate-box--transfer form__spacer border">
                    <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-2') ?></h3>
                </div>
                <p><?= Yii::t('support', 'donate-text-2') ?></p>
            </div>
        </div>
    </div>
    <div class="container-fluid donation-background">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="donate-box--transfer form__spacer border reduce-margin">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-3') ?></h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <a id="mas-osszeg"></a>
                    <form id="donate-box-paypal"
                          class="donate-box-paypal form__spacer donate-box--transfer transfer-box">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'paypal') ?></h3>
                        <h4 class="heading--4"><?= Yii::t('support', 'amount') ?></h4>
                        <input type="button" class="button button--donate active" data-value="1000"
                               name="amount" value="1000 Ft"/>
                        <input type="button" class="button button--donate" data-value="3000" name="amount"
                               value="3000 Ft"/>
                        <input type="button" class="button button--donate" data-value="5000" name="amount"
                               value="5000 Ft"/>
                        <input type="button" class="button button--donate" data-value="10000" name="amount"
                               value="10000 Ft"/>
                        <input type="button" class="button button--donate button-donate-other" data-value=""
                               value="<?= Yii::t('support', 'other-amount') ?>"/>
                        <br>
                        <div id="paypal-other-amount" class="row" style="display: none;">
                            <div class="col-xs-12 col-sm-4">
                                <div class="input-group input-group--addon">
                                    <div class="form__row">
                                        <input id="other-amount-input" class="input input--default"
                                               type="number" name="amount">
                                        <div class="input-group__addon">
                                            <div class="input-group__addon__text">Ft</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4 class="heading--4"><?= Yii::t('support', 'frequency') ?></h4>
                        <div id="donate-frequency" class="radio--switch">
                            <input id="single" type="radio" name="donate-frequency" radio-autodiscover="0"
                                   value="single">
                            <label for="single"><?= Yii::t('support', 'frequency-once') ?></label>
                            <input id="monthly" type="radio" name="donate-frequency" radio-autodiscover="0"
                                   value="monthly" checked>
                            <label for="monthly"><?= Yii::t('support', 'frequency-monthly') ?></label>
                        </div>
                        <div class="br"></div>
                        <button id="paypal-donate-submit"
                                class="button button--large button--paypal init-loader-button" type="submit"
                                name="donate"><?= SVG::icon(SVG::ICON_PAYPAL, ['class' => 'icon icon--large']) ?><?= Yii::t('support', 'btn-support') ?></button>
                    </form>

                    <form id="paypal-donate-form" action="https://www.paypal.com/cgi-bin/webscr" method="post"
                          target="_top">
                        <input type="hidden" name="charset" value="utf-8">
                        <input type="hidden" name="cmd" value="_donations">
                        <input type="hidden" name="business" value="sajto@myproject.hu">
                        <input type="hidden" name="lc" value="HU">
                        <input type="hidden" name="item_name" value="myProject">
                        <input type="hidden" name="amount" value=""/>
                        <input type="hidden" name="currency_code" value="HUF">
                        <input type="hidden" name="no_note" value="0">
                        <input type="hidden" name="bn"
                               value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif"
                             width="1" height="1">
                    </form>

                    <form id="paypal-subscribe-form" action="https://www.paypal.com/cgi-bin/webscr"
                          method="post" target="_top">
                        <input type="hidden" name="cmd" value="_xclick-subscriptions"/>
                        <input type="hidden" name="business" value="sajto@myproject.hu"/>
                        <input type="hidden" name="lc" value="HUF"/>
                        <input type="hidden" name="no_note" value="1"/>
                        <input type="hidden" name="src" value="1"/>
                        <input type="hidden" name="a3" value=""/>
                        <input type="hidden" name="p3" value="1"/>
                        <input type="hidden" name="t3" value="M"/>
                        <input type="hidden" name="currency_code" value="HUF"/>
                        <input type="hidden" name="bn"
                               value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest"/>
                    </form>
                </div> <!-- end col -->

                <div class="col-xs-12 col-lg-6">
                    <div class="donate-box--transfer form__spacer transfer-box">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'donate-bank-transfer') ?></h3>
                        <p><?= Yii::t('support', 'donate-details') ?></p>
                        <p><?= Yii::t('support', 'donate-details-content') ?></p>
                        <p><?= Yii::t('support', 'donate-details-content2') ?></p>
                        <p><?= Yii::t('support', 'donate-footer') ?></p>
                    </div>
                </div>
            </div> <!-- end row -->

            <div class="row" style="margin-top: 20px; padding-bottom: 20px;">
                <div class="col-xs-12">
                    <div class="donate-box--transfer form__spacer reduce-margin">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-4') ?></h3>
                    </div>
                    <p><?= Yii::t('support', 'donate-text-4') ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="donate-box--transfer form__spacer border">
                    <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-5') ?></h3>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 40px;">
            <div class="col-xs-12 col-lg-9">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-xs-12 col-sm-6">
                        <div class="donation-box donation-box__center">
                                        <span style="border-bottom: 3px solid #fff; padding-bottom: 5px;">
                                            <span style="font-size: 5em;">8</span> <span
                                                style="font-size: 1.5em;">ÉV</span>
                                        </span>
                            <div style="line-height: 1.4em; margin-top: -5px;">
                                <div style="font-size: 1.5em;">40 000</div>
                                <div>megoldott</div>
                                <div><span>lakossági bejelentés</span></div>
                            </div>

                            <img style="position: relative; width: 160px    ; top: -135px; left: 35px;"
                                 src="<?= $assetUrl ?>/images/pipe.png">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-xs-12 col-sm-6">
                        <div class="donation-box__center donation-box posrel">
                            <div class="vertically-center">
                                <div style="font-size: 1.4em">több száz</div>
                                <div>lakossági bejelentés</div>
                                <div>érkezik naponta</div>
                            </div>
                            <div style="position: absolute;">
                                <img style="position: relative; width: 160px; top: -40px; left: 60px;"
                                     src="<?= $assetUrl ?>/images/info.png">
                            </div>

                            <div style="position: absolute" class="chevronimage">
                                <div style="position: relative; left: 200px;">
                                    <img style="width: 160px;" src="<?= $assetUrl ?>/images/chevron.png">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-xs-12 col-sm-6">
                        <div class="donation-box donation-box__center posrel">
                            <div class="vertically-center">
                                <div>ebből évente</div>
                                <div style="font-size: 1.4em">5 000 ügy</div>
                                <div>sikeresen meg is oldódik</div>
                            </div>
                            <div style="position: absolute;">
                                <img style="position: relative; width: 160px; top: -20px; left: 60px;"
                                     src="<?= $assetUrl ?>/images/smile.png">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xs-12 visible-mobile">
                        <div class="donation-box posrel">
                            <div class="vertically-center donation-box__building">
                                <div style="font-size: 6em; line-height: 0.8em">24</div>
                                <div>város csatlakozott már</div>
                                <div>a myProject rendszeréhez</div>
                            </div>
                            <div style="position: absolute;">
                                <img class="building_image" src="<?= $assetUrl ?>/images/building.png">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12 visible-mobile">
                        <div class="donation-box posrel">
                            <div class="vertically-center donation-box__office-box">
                                <div style="padding:5px">Szoros együttműködés önkormányzatokkal,
                                    közműszolgáltatókkal <br>és a BKK-val
                                </div>
                            </div>
                            <div style="position: absolute;">
                                <img class="office_img"
                                     src="<?= $assetUrl ?>/images/office.png">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12 visible-mobile">
                        <div class="donation-box">
                            <img style="display: block; margin-left: auto; margin-right: auto; width: 100px;"
                                 src="<?= $assetUrl ?>/images/star.png">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center; font-size: 10px">
                                    <?= Yii::t('support', 'donate-container-text') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hide-mobile">
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-lg-5 col-md-4 col-sm-6 col-xs-12">
                            <div class="donation-box posrel">
                                <div class="vertically-center" style="left: 40%;">
                                    <div style="font-size: 6em; line-height: 0.8em">24</div>
                                    <div>város csatlakozott már</div>
                                    <div>a myProject rendszeréhez</div>
                                </div>
                                <div style="position: absolute;">
                                    <img class="building_image" src="<?= $assetUrl ?>/images/building.png">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-4 col-sm-6 col-xs-12">
                            <div class="donation-box posrel">
                                <div class="vertically-center donation-box__office-box">
                                    <div style="padding:5px">Szoros együttműködés önkormányzatokkal,
                                        közműszolgáltatókkal <br>és a BKK-val
                                    </div>
                                </div>
                                <div style="position: absolute;">
                                    <img class="office_img"
                                         src="<?= $assetUrl ?>/images/office.png">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12 star-box-container-tablet visible-mobile">
                            <div class="donation-box">
                                <img class="image_star" src="<?= $assetUrl ?>/images/star.png">
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center; font-size: 10px;">
                                        <?= Yii::t('support', 'donate-container-text') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 star-box-container">
                <div class="donation-box" style="height: 460px">
                    <img class="image__star" style="" src="<?= $assetUrl ?>/images/star.png">
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center">
                            <?= Yii::t('support', 'donate-container-text') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="container-fluid donation-background">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="donate-box--transfer form__spacer border">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-6') ?></h3>
                    </div>
                    <p><?= Yii::t('support', 'donate-text-6') ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid donation-background__footer">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="donate-box--transfer form__spacer border">
                        <h3 class="heading heading--3"><?= Yii::t('support', 'donate-title-7') ?></h3>
                    </div>
                    <p><?= Yii::t('support', 'donate-text-7') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
