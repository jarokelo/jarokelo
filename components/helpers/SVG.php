<?php

namespace app\components\helpers;

use Yii;

class SVG
{
    const ICON_BLOG = 'blog';
    const ICON_INSTITUTION = 'institution';
    const ICON_CATEGORY = 'category';
    const ICON_CAMERA = 'camera';
    const ICON_CHECKED_DOCUMENT = 'checked-document';
    const ICON_CHECKED_MAIL = 'checked-mail';
    const ICON_CHEVRON_DOWN = 'chevron-down';
    const ICON_CHEVRON_UP = 'chevron-up';
    const ICON_CHEVRON_RIGHT = 'chevron-right';
    const ICON_CHEVRON_LEFT = 'chevron-left';
    const ICON_CHECKMARK = 'checkmark';
    const ICON_CIRCLE_USER = 'circle-user';
    const ICON_CITY = 'city';
    const ICON_CLOCK = 'clock';
    const ICON_CLOSE = 'close';
    const ICON_CLOSE_WHITE = 'close-white';
    const ICON_DISLIKE = 'dislike';
    const ICON_DOCUMENT = 'document';
    const ICON_DOCUMENTS = 'documents';
    const ICON_DOWNLOAD = 'download';
    const ICON_EMAIL = 'email';
    const ICON_EMAIL_OPEN = 'email-open';
    const ICON_EXCLAMATION_MARK = 'exclamation-mark';
    const ICON_FACEBOOK_ALT = 'facebook-alt';
    const ICON_FACEBOOK = 'facebook';
    const ICON_FLAG = 'flag';
    const ICON_GOOGLE = 'google';
    const ICON_INSTAGRAM = 'instagram';
    const ICON_PAYPAL = 'paypal';
    const ICON_PEN = 'pen';
    const ICON_LIKE = 'like';
    const ICON_LOCK = 'lock';
    const ICON_LOGO = 'logo';
    const ICON_MAGNIFIY = 'magnify';
    const ICON_MAP = 'map';
    const ICON_MAP_POI = 'map-poi';
    const ICON_MENU = 'menu';
    const ICON_POI = 'poi';
    const ICON_RSS = 'rss';
    const ICON_SEND = 'send';
    const ICON_EYE = 'eye';
    const ICON_TWITTER = 'twitter';
    const ICON_TARGET = 'target';
    const ICON_GENERAL_INFO = 'general-info';
    const ICON_IMAGES = 'images';
    const ICON_VIDEO_CAMERA = 'video-camera';
    const ICON_WHAT = 'what';

    /**
     *
     * This code
     *
     * ```php
     *     <?= SVG::icon(SVG::ICON_FACEBOOK, ['class' => 'icon list__icon footer__social-list__icon']) ?>
     * ```
     * renders this html
     *
     * ```html
     *     <svg class="icon list__icon footer__social-list__icon">
     *         <use xlink:href="/assets/hash1/hash2/images/icons.svg#icon-facebook"></use>
     *     </svg>
     * ```
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function icon($name, $options = [])
    {

        $svgOptions = array_merge([
            'class' => 'icon',
        ], $options);

        $bundleUrl = \app\assets\AppAsset::register(Yii::$app->view)->baseUrl;

        $useTag = Html::tag('use', null, ['xlink:href' => $bundleUrl . '/images/icons.svg#icon-' . $name]);

        return Html::tag('svg', $useTag, $svgOptions);
    }
}
