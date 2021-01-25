<?php
use app\components\helpers\Link;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $name string name of the user */
?>

<!-- start content (welcom after reg) -->
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="title title--main small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-bottom:15px;text-align:left"><?= Yii::t('email', 'hello_with_name', ['name' => $name]) ?></th>
                                            <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0!important;padding-bottom:15px;text-align:left;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left">Üdvözlünk a myProject felhasználói között. Megtetted az első lépést, hogy a városod élhetőbb és szebb legyen.</p>
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left">Itt egy pár tanácsot tőlünk a kezdéshez:</p>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="list small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <table class="row list__item" style="border-collapse:collapse;border-spacing:0;margin-bottom:30px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-4 large-2 columns first" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <div class="list__order">
                                                    <img src="<?= $message->embed(Yii::getAlias('@mailImages/ordered_bullet_num_1.png')) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto">
                                                </div>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-8 large-10 columns last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:83.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:0;margin-top:5px;padding:0;text-align:left"><a href="<?= Link::to(Link::PROFILE_MANAGE) ?>" style="Margin:0;color:#5289C8;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none">Add meg városod (és kerületed)</a> a Profilod alatt, így rögtön láthatod, hogy milyen bejelentések vannak környezetedben.</p>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row list__item" style="border-collapse:collapse;border-spacing:0;margin-bottom:30px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-4 large-2 columns first" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <div class="list__order">
                                                    <img src="<?= $message->embed(Yii::getAlias('@mailImages/ordered_bullet_num_2.png')) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto">
                                                </div>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-8 large-10 columns last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:83.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:0;margin-top:5px;padding:0;text-align:left"><a href="<?= Url::to(['report/create'], true) ?>" style="Margin:0;color:#5289C8;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none">Küldd el első bejelentésed</a>: töltsd ki az űrlapot és add meg a szükséges információt ahhoz, hogy tudjunk segíteni a megoldásban.</p>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row list__item" style="border-collapse:collapse;border-spacing:0;margin-bottom:30px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-4 large-2 columns first" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <div class="list__order">
                                                    <img src="<?= $message->embed(Yii::getAlias('@mailImages/ordered_bullet_num_3.png')) ?>" style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto">
                                                </div>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-8 large-10 columns last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:83.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                                                <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:0;margin-top:5px;padding:0;text-align:left"><a href="<?= Link::to(link::REPORTS_NEARBY) ?>" style="Margin:0;color:#5289C8;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none">Nézd meg a környék bejelentéseit</a> és visszajelzéseddel segíts abban, hogy lezárhassuk a problémákat.</p>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<!-- end content (welcom after reg) -->

<?= $this->render('@app/mail/layouts/_about', ['message' => $message]) ?>
