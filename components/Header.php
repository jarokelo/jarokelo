<?php

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class Header
 *
 * @package app\components
 */
class Header
{
    const TYPE_CANONICAL = 'canonical';
    const TYPE_KEYWORDS = 'keywords';
    const TYPE_DESCRIPTION = 'description';
    const TYPE_TITLE = 'title';
    const TYPE_ROBOTS = 'robots';

    const FB_TYPE_ARTICLE = 'article';
    const FB_TYPE_WEBSITE = 'website';

    const TYPE_AUTHOR = 'author';
    const TYPE_PLACE_LOCATION_LATITUDE = 'place:location:latitude';
    const TYPE_PLACE_LOCATION_LONGITUDE = 'place:location:longitude';

    const TYPE_FB_TITLE = 'og:title';
    const TYPE_FB_SITE_NAME = 'og:site_name';
    const TYPE_FB_DESCRIPTION = 'og:description';
    const TYPE_FB_IMAGE = 'og:image';
    const TYPE_FB_TYPE = 'og:type';
    const TYPE_FB_LOCALE = 'og:locale';
    const TYPE_FB_SHARE_URL = 'og:url';
    const TYPE_FB_APP_ID = 'og:app_id';
    const TYPE_FB_LATITUDE = 'og:latitude';
    const TYPE_FB_LONGITUDE = 'og:longitude';
    const TYPE_FB_ADMINS = 'fb:admins';

    const SHARE_IMAGE_120 = '@web/images/share_120x120.png';
    const SHARE_IMAGE_800 = '@web/images/share_800x800.png';
    const SHARE_DONATION = '@web/images/share_donation.jpg';

    /**
     * Registers a meta tag.
     *
     * @param string $type
     * @param mixed $content
     *
     * @return void
     */
    public static function registerTag($type, $content)
    {
        $view = Yii::$app->getView();
        if (empty($content)) {
            return;
        }

        switch ($type) {
            case self::TYPE_TITLE:
                $view->title = $content . ' - ' . Yii::$app->name;
                break;
            case self::TYPE_KEYWORDS:
            case self::TYPE_DESCRIPTION:
            case self::TYPE_ROBOTS:
            case self::TYPE_AUTHOR:
                $view->registerMetaTag([
                    'name' => $type,
                    'content' => $content,
                ], $type);
                break;
            case self::TYPE_FB_TITLE:
            case self::TYPE_FB_SITE_NAME:
            case self::TYPE_FB_TYPE:
            case self::TYPE_FB_SHARE_URL:
            case self::TYPE_FB_LOCALE:
            case self::TYPE_FB_DESCRIPTION:
            case self::TYPE_FB_IMAGE:
            case self::TYPE_FB_APP_ID:
            case self::TYPE_FB_ADMINS:
            case self::TYPE_FB_LATITUDE:
            case self::TYPE_FB_LONGITUDE:
            case self::TYPE_PLACE_LOCATION_LATITUDE:
            case self::TYPE_PLACE_LOCATION_LONGITUDE:
                $view->registerMetaTag([
                    'property' => $type,
                    'content' => $content,
                ], $type);
                break;
            case self::TYPE_CANONICAL:
                $view->registerLinkTag([
                    'rel' => $type,
                    'href' => $content,
                ]);
                break;
            default:
                break;
        }
    }

    /**
     * Sets all predefined meta tags in the array and saves them.
     *
     * @param array $tags
     *
     * @return void
     */
    public static function setAll($tags)
    {
        self::setStatics();

        foreach ($tags as $type => $content) {
            static::registerTag($type, $content);
        }
    }

    private static function setStatics()
    {
        static::registerTag(self::TYPE_AUTHOR, Yii::t('meta', 'author.content'));
        static::registerTag(self::TYPE_FB_SITE_NAME, Yii::t('meta', 'og.site_name'));
        static::registerTag(self::TYPE_FB_TITLE, Yii::t('meta', 'title.default'));
        static::registerTag(self::TYPE_TITLE, Yii::t('meta', 'title.default'));
        static::registerTag(self::TYPE_DESCRIPTION, Yii::t('meta', 'description.default'));
        static::registerTag(self::TYPE_FB_DESCRIPTION, Yii::t('meta', 'description.default'));
        static::registerTag(self::TYPE_FB_APP_ID, ArrayHelper::getValue(Yii::$app->authClientCollection, 'clients.facebook.clientId'));
        static::registerTag(self::TYPE_FB_TYPE, self::FB_TYPE_WEBSITE);
        static::registerTag(self::TYPE_FB_IMAGE, Url::to(self::SHARE_IMAGE_800, true));
    }
}
