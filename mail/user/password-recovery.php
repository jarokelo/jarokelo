<?php

use yii\helpers\Html;

/* @var $name string name of the user */
/* @var \app\models\forms\PasswordRecoveryForm $form */
/* @var \yii\mail\BaseMessage $message instance of newly created mail message */
?>

<!-- start content (elfelejtett-jelszo) -->
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
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left"><?= Yii::t('email', 'password-recovery.body') ?></p>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="btn btn--green btn--wide small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:20px;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="background:#9BD158;border-collapse:collapse;border-radius:65px;border-spacing:0;margin:15px auto 15px auto;padding:0;text-align:left;vertical-align:top;width:auto">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;width:300px">
                        <?= Html::a(Yii::t('email', 'password-recovery.link'), $form->generateNewPasswordUrl(), ['style' => 'Margin:0;color:#fff;display:block;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-bottom:13px;padding-left:10px;padding-right:10px;padding-top:12px;text-align:center;text-decoration:none']) ?>
                    </th>
                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<table class="row mb-20" style="border-collapse:collapse;border-spacing:0;margin-bottom:20px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left"><?= Yii::t('email', 'password-recovery.body_end') ?></p>
                    </th>
                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<!-- end content (elfelejtett-jelszo) -->
