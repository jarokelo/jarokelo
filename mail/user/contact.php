<?php

use app\components\helpers\Html;

/* @var string $name */
/* @var string $email */
/* @var string $message */
/* @var \yii\web\View $this */
/* @var $baseMessage \yii\mail\BaseMessage instance of newly created mail message */
?>
<tr>
    <td valign="top" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: Arial, Helvetica, sans-serif; padding: 0; vertical-align: top;">
        <table id="mainTable" valign="top" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #ffffff; border: 0; border-collapse: collapse; cellpadding: 0; cellspacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <tr>
                <td class="side-padding--main" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; padding: 0; vertical-align: top; width: 45px;"></td>
                <td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: Arial, Helvetica, sans-serif; padding: 0; vertical-align: top;">
                    <table class="main__content" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border: 0; border-collapse: collapse; cellpadding: 0; cellspacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                        <tr>
                            <td class="heading heading--h1 padding-top--xl padding-bottom--m" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #565656; font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: 400; line-height: 30px; padding: 0; padding-bottom: 24px; padding-top: 48px; text-align: left; vertical-align: top;"><?= Yii::t('email', 'hello') ?></td>
                            <td class="p" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #565656; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 24px; padding: 0; vertical-align: top;"></td>
                        </tr>
                        <tr>
                            <td class="p" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #565656; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 24px; padding: 0; vertical-align: top;">

                                <p><?= Yii::t('email', 'contact.title', ['name' => Yii::$app->name]) ?></p>

                                <p><b><?= Yii::t('email', 'contact.name') ?>:</b> <?= $name ?></p>

                                <p><b><?= Yii::t('email', 'contact.email') ?>:</b> <?= $email ?></p>

                                <p><b><?= Yii::t('email', 'contact.message') ?>:</b><br/><?= Html::formatText($message) ?></p>

                                <p><?= Yii::t('email', 'signature') ?></p>

                            </td>
                        </tr>
                    </table>
                    <!-- end main__content -->
                </td>
                <td class="side-padding--main" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; padding: 0; vertical-align: top; width: 45px;"></td>
            </tr>
        </table>
        <!-- end #mainTable -->
</tr>
