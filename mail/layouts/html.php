<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var \yii\mail\BaseMessage $content */
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="hu">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body style="-moz-box-sizing:border-box;-ms-text-size-adjust:100%;-webkit-box-sizing:border-box;-webkit-text-size-adjust:100%;Margin:0;box-sizing:border-box;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;min-width:100%;padding:0;text-align:left;width:100%!important">
<?php $this->beginBody() ?>
<style>@media only screen {
        html {
            min-height: 100%;
            background: #fff
        }
    }

    @media only screen and (max-width: 645px) {
        .small-float-center {
            margin: 0 auto !important;
            float: none !important;
            text-align: center !important
        }

        .small-text-center {
            text-align: center !important
        }

        .small-text-left {
            text-align: left !important
        }

        .small-text-right {
            text-align: right !important
        }
    }

    @media only screen and (max-width: 645px) {
        .hide-for-large {
            display: block !important;
            width: auto !important;
            overflow: visible !important;
            max-height: none !important;
            font-size: inherit !important;
            line-height: inherit !important
        }
    }

    @media only screen and (max-width: 645px) {
        table.body table.container .hide-for-large, table.body table.container .row.hide-for-large {
            display: table !important;
            width: 100% !important
        }
    }

    @media only screen and (max-width: 645px) {
        table.body table.container .callout-inner.hide-for-large {
            display: table-cell !important;
            width: 100% !important
        }
    }

    @media only screen and (max-width: 645px) {
        table.body table.container .show-for-large {
            display: none !important;
            width: 0;
            mso-hide: all;
            overflow: hidden
        }
    }

    @media only screen and (max-width: 645px) {
        table.body img {
            width: auto;
            height: auto
        }

        table.body center {
            min-width: 0 !important
        }

        table.body .container {
            width: 95% !important
        }

        table.body .column, table.body .columns {
            height: auto !important;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding-left: 45px !important;
            padding-right: 45px !important
        }

        table.body .column .column, table.body .column .columns, table.body .columns .column, table.body .columns .columns {
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        table.body .collapse .column, table.body .collapse .columns {
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        td.small-1, th.small-1 {
            display: inline-block !important;
            width: 8.33333% !important
        }

        td.small-2, th.small-2 {
            display: inline-block !important;
            width: 16.66667% !important
        }

        td.small-3, th.small-3 {
            display: inline-block !important;
            width: 25% !important
        }

        td.small-4, th.small-4 {
            display: inline-block !important;
            width: 33.33333% !important
        }

        td.small-5, th.small-5 {
            display: inline-block !important;
            width: 41.66667% !important
        }

        td.small-6, th.small-6 {
            display: inline-block !important;
            width: 50% !important
        }

        td.small-7, th.small-7 {
            display: inline-block !important;
            width: 58.33333% !important
        }

        td.small-8, th.small-8 {
            display: inline-block !important;
            width: 66.66667% !important
        }

        td.small-9, th.small-9 {
            display: inline-block !important;
            width: 75% !important
        }

        td.small-10, th.small-10 {
            display: inline-block !important;
            width: 83.33333% !important
        }

        td.small-11, th.small-11 {
            display: inline-block !important;
            width: 91.66667% !important
        }

        td.small-12, th.small-12 {
            display: inline-block !important;
            width: 100% !important
        }

        .column td.small-12, .column th.small-12, .columns td.small-12, .columns th.small-12 {
            display: block !important;
            width: 100% !important
        }

        table.body td.small-offset-1, table.body th.small-offset-1 {
            margin-left: 8.33333% !important;
            Margin-left: 8.33333% !important
        }

        table.body td.small-offset-2, table.body th.small-offset-2 {
            margin-left: 16.66667% !important;
            Margin-left: 16.66667% !important
        }

        table.body td.small-offset-3, table.body th.small-offset-3 {
            margin-left: 25% !important;
            Margin-left: 25% !important
        }

        table.body td.small-offset-4, table.body th.small-offset-4 {
            margin-left: 33.33333% !important;
            Margin-left: 33.33333% !important
        }

        table.body td.small-offset-5, table.body th.small-offset-5 {
            margin-left: 41.66667% !important;
            Margin-left: 41.66667% !important
        }

        table.body td.small-offset-6, table.body th.small-offset-6 {
            margin-left: 50% !important;
            Margin-left: 50% !important
        }

        table.body td.small-offset-7, table.body th.small-offset-7 {
            margin-left: 58.33333% !important;
            Margin-left: 58.33333% !important
        }

        table.body td.small-offset-8, table.body th.small-offset-8 {
            margin-left: 66.66667% !important;
            Margin-left: 66.66667% !important
        }

        table.body td.small-offset-9, table.body th.small-offset-9 {
            margin-left: 75% !important;
            Margin-left: 75% !important
        }

        table.body td.small-offset-10, table.body th.small-offset-10 {
            margin-left: 83.33333% !important;
            Margin-left: 83.33333% !important
        }

        table.body td.small-offset-11, table.body th.small-offset-11 {
            margin-left: 91.66667% !important;
            Margin-left: 91.66667% !important
        }

        table.body table.columns td.expander, table.body table.columns th.expander {
            display: none !important
        }

        table.body .right-text-pad, table.body .text-pad-right {
            padding-left: 10px !important
        }

        table.body .left-text-pad, table.body .text-pad-left {
            padding-right: 10px !important
        }

        table.menu {
            width: 100% !important
        }

        table.menu td, table.menu th {
            width: auto !important;
            display: inline-block !important
        }

        table.menu.small-vertical td, table.menu.small-vertical th, table.menu.vertical td, table.menu.vertical th {
            display: block !important
        }

        table.menu[align=center] {
            width: auto !important
        }

        table.button.small-expand, table.button.small-expanded {
            width: 100% !important
        }

        table.button.small-expand table, table.button.small-expanded table {
            width: 100%
        }

        table.button.small-expand table a, table.button.small-expanded table a {
            text-align: center !important;
            width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        table.button.small-expand center, table.button.small-expanded center {
            min-width: 0
        }
    }</style>
<?php
$showHeader = true;
$showFooter = true;

foreach ($this->params as $param) {
    if (isset($param['showHeader']) && $param['showHeader'] === false) {
        $showHeader = false;
    }

    if (isset($param['showFooter']) && $param['showFooter'] === false) {
        $showFooter = false;
    }
}
/*
<span class="preheader" style="color:#fff;display:none!important;font-size:1px;line-height:1px;max-height:0;max-width:0;mso-hide:all!important;opacity:0;overflow:hidden;visibility:hidden">Content preview</span>
*/
?>
<table class="body" style="Margin:0;background:#fff;border-collapse:collapse;border-spacing:0;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%">
    <tr style="padding:0;text-align:left;vertical-align:top">
        <td class="center" align="center" valign="top" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
            <table class="container-wrapper" align="center" style="border-collapse:collapse;border-spacing:0;max-width:600px;padding:0;text-align:left;vertical-align:top">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <td class="container" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#444;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:600px;word-wrap:break-word">
                        <!-- start eDM body -->
                        <?php if ($showHeader): ?>
                            <?= $this->render('_header', ['message' => $message]) ?>
                        <?php endif ?>

                        <?= $content ?>

                        <?php if ($showFooter): ?>
                            <?= $this->render('_footer', ['message' => $message]) ?>
                        <?php endif ?>
                        <!-- end eDM body -->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="display:none;white-space:nowrap;font:15px courier;line-height:0"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
