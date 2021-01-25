<?php
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $name string name of the user */
/* @var $infoTxt string info text describing the activity status change */
/* @var \app\models\db\Report $report */
?>

<!-- start content (azonnali) -->
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
                        <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left"><?= $infoTxt ?></p>
                        <?php if (isset($voteForUs) && $voteForUs != ''): ?>
                            <p style="Margin:0;Margin-bottom:10px;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;margin-bottom:20px;padding:0;text-align:left"><?= $voteForUs ?></p>
                        <?php endif;?>
                        <?= $this->render('@app/mail/layouts/_report_block', ['message' => $message, 'report' => $report]) ?>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<!-- end content (azonnali) -->

<?= $this->render('@app/mail/layouts/_support', ['message' => $message]) ?>
