<?php
use app\components\helpers\Link;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
?>

<!-- start footer -->
<table class="row footer" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
        <th class="footer__inner small-12 large-12 columns first last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:40px;padding-left:45px;padding-right:45px;text-align:center;width:555px">
            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center">
                        <table class="row footer__nav" style="border-collapse:collapse;border-spacing:0;margin-bottom:15px;margin-top:35px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="footer__nav-item small-12 large-1 columns first" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:8.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;margin-bottom:15px;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="<?= Link::to(Link::REPORTS) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none;text-transform:uppercase"><?= Yii::t('menu', 'report') ?></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="footer__nav-item small-12 large-10 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:83.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;margin-bottom:15px;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="<?= Link::to(Link::CREATE_REPORT) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none;text-transform:uppercase"><?= Yii::t('menu', 'new_report') ?></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="footer__nav-item small-12 large-1 columns last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:8.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;margin-bottom:15px;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="<?= Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_CONTACT]) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none;text-transform:uppercase"><?= Yii::t('menu', 'contact') ?></a></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><?= Yii::$app->params['mobile']['enabled'] ? Yii::t('app', 'appbox.content1') : Yii::t('app', 'appbox.content1.disabled') ?></th>
                                            <th class="expander" style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;text-align:center;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <?php if (Yii::$app->params['mobile']['enabled']): ?>
                        <table class="row footer__download" style="border-collapse:collapse;border-spacing:0;margin-top:15px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="footer__img-wrapper small-12 large-6 columns first" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:50%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:10px;padding-right:10px;text-align:center"><a href="<?= Yii::$app->params['mobile']['links']['ios'] ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/badge-app-store.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-bottom:30px;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="footer__img-wrapper small-12 large-6 columns last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:50%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;padding-left:10px;padding-right:10px;text-align:center"><a href="<?= Yii::$app->params['mobile']['links']['android'] ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/badge-google-play.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-bottom:30px;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <?php endif; ?>
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><?= Yii::t('label', 'footer.follow_us') ?></th>
                                            <th class="expander" style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;text-align:center;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row footer__social" style="border-collapse:collapse;border-spacing:0;margin-bottom:10px;margin-top:10px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-1 large-1 columns first" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:8.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-2 large-2 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="#" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/icon_social_fb.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-2 large-2 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="#" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/icon_social_instagram.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-2 large-2 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="#" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/icon_social_twitter.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-2 large-2 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="#" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/icon_social_b.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-2 large-2 columns" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:16.66667%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><a href="<?= Url::to(['rss/index'], true) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img class="footer__img" src="<?= $message->embed(Yii::getAlias('@mailImages/icon_social_rss.png')) ?>" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:100%;outline:0;text-decoration:none;width:auto"></a></th>
                                        </tr>
                                    </table>
                                </th>
                                <th class="small-1 large-1 columns last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:8.33333%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row has-bottom-spacing" style="border-collapse:collapse;border-spacing:0;margin-bottom:30px;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center"><?= Yii::t('email', 'footer.too_many_emails') ?> <a class="has-underline" href="<?= Link::to(link::PROFILE_MANAGE) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:underline"><?= Yii::t('email', 'footer.too_many_emails_link_to_profile') ?></a>.</th>
                                            <th class="expander" style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;text-align:center;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                        <table class="row" style="border-collapse:collapse;border-spacing:0;padding:0;position:relative;text-align:left;vertical-align:top;width:100%"><tbody><tr style="padding:0;text-align:left;vertical-align:top">
                                <th class="small-12 large-12 columns first last" style="Margin:0 auto;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:0!important;padding-right:0!important;text-align:center;width:100%">
                                    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                        <tr style="padding:0;text-align:left;vertical-align:top">
                                            <th style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:center">&copy; <?= date('Y') ?> <a href="<?= Url::to(['/'], true) ?>" style="Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><?= Yii::t('meta', 'og.site_name') ?></a></th>
                                            <th class="expander" style="Margin:0;background:#444;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:300;line-height:1.3;margin:0;padding:0!important;text-align:center;visibility:hidden;width:0"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr></tbody></table>
                    </th>
                </tr>
            </table>
        </th>
    </tr></tbody></table>
<!-- end footer -->
