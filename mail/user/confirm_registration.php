<?php

/**
 * @var \yii\web\View $this
 * @var string $link
 * @var string $name
 */

use yii\helpers\Url;
?>
<table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
    <tbody>
        <tr style="padding:0;text-align:left;vertical-align:top">
            <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:45px;padding-right:45px;text-align:left;width:555px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
                                <tbody>
                                    <tr style="padding:0;text-align:left;vertical-align:top">
                                        <th class="title title--main small-12 large-12 columns first last" style="Margin:0 auto;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:left;width:100%">
                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                <tr style="padding:0;text-align:left;vertical-align:top">
                                                    <th style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-bottom:15px;text-align:left">
                                                        <?= Yii::t('email', 'hello_with_name', ['name' => $name]) ?>
                                                    </th>
                                                    <th class="expander" style="Margin:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:1.3;margin:0;padding:0!important;padding-bottom:15px;text-align:left;visibility:hidden;width:0"></th>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin-bottom:30px;padding:0;text-align:left">
                                <?= Yii::t('auth', 'click-to-confirm', ['link' => $link]) ?>
                            </p>
                        </th>
                    </tr>
                </table>
            </th>
        </tr>
    </tbody>
</table>

<?= $this->render('@app/mail/layouts/_about', ['message' => $message, 'link' => Url::to(['about/how-it-works'], true)]) ?>