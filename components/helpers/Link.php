<?php

namespace app\components\helpers;

use yii\helpers\Url;

class Link
{
    const HOME = '/fooldal';

    const ADMIN = '/citizen';

    const POSTFIX_FEATURED = 'kiemelt';
    const POSTFIX_FRESH = 'friss';
    const POSTFIX_NEARBY = 'hozzam-kozel';
    const POSTFIX_REPORT_ANONYMOUS = 'nevtelen';
    const POSTFIX_REPORT_COMMENT = 'komment';
    const POSTFIX_REPORT_PDF = 'pdf';
    const POSTFIX_REPORT_EDM = 'hivatal';

    const REPORTS = 'bejelentesek';
    const REPORTS_HIGHLIGHTED = self::REPORTS . '/' . self::POSTFIX_FEATURED;
    const REPORTS_FRESH = self::REPORTS . '/' . self::POSTFIX_FRESH;
    const REPORTS_NEARBY = self::REPORTS . '/' . self::POSTFIX_NEARBY;

    const AUTH_LOGIN = 'bejelentkezes';
    const AUTH_ERROR = 'hiba';
    //const AUTH_AUTH = 'hitelesites';
    //const AUTH_FACEBOOK = self::AUTH_AUTH . '/facebook';
    //const AUTH_GOOGLE = self::AUTH_AUTH . '/google';
    const AUTH_AUTH = 'auth';
    const AUTH_FACEBOOK = self::AUTH_AUTH . '?authClient=facebook';
    const AUTH_GOOGLE = self::AUTH_AUTH . '?authClient=google';
    const AUTH_LOGOUT = 'kijelentkezes';
    const AUTH_REGISTER = 'regisztracio';
    const AUTH_PASSWORD_RECOVERY = 'elfelejtett-jelszo';
    const AUTH_SET_NEW_PASSWORD = 'uj-jelszo-beallitas';

    const POSTFIX_LOGIN_FROM_NEW_REPORT = 'nevtelen-bejelentes';

    const ABOUT = 'rolunk';
    const POSTFIX_ABOUT_CONTACT = 'kapcsolat';
    const POSTFIX_ABOUT_TEAM = 'csapat';
    const POSTFIX_ABOUT_ASSOCIATION = 'egyesulet';
    const POSTFIX_ABOUT_SUPPORT = 'tamogass';
    const POSTFIX_ABOUT_HOWITWORKS = 'hogyan-mukodik';
    const POSTFIX_ABOUT_VOLUNTEER = 'csatlakozz';
    const POSTFIX_ABOUT_PARTNERS = 'partnerek';
    const POSTFIX_ABOUT_TOS = 'felhasznalasi-feltetelek';
    const POSTFIX_ABOUT_BUREAU = 'hivatal';
    const POSTFIX_ABOUT_ANNUALREPORTS = 'egyesulet';

    const CREATE_REPORT = 'uj-bejelentes';
    const CREATE_REPORT_SUCCESS = 'sikeres';

    const STATISTICS = 'statisztikak';
    const POSTFIX_STATISTICS_CITIES = 'varosok';
    const POSTFIX_STATISTICS_INSTITUTIONS = 'illetekesek';
    const POSTFIX_STATISTICS_USERS = 'felhasznalok';

    const MAP = 'terkep';

    const ANONYMOUS_REPORT = self::CREATE_REPORT . '/' . self::POSTFIX_REPORT_ANONYMOUS;
    const LOGIN_FROM_NEW_REPORT = self::AUTH_LOGIN . '/' . self::POSTFIX_LOGIN_FROM_NEW_REPORT;

    const PROFILE = 'profil';
    const PROFILES = 'felhasznalok';
    const POSTFIX_PROFILE_DRAFTS = 'piszkozatok';
    const POSTFIX_PROFILE_MANAGE = 'kezeles';
    const PROFILE_DRAFTS = self::PROFILE . '/' . self::POSTFIX_PROFILE_DRAFTS;
    const PROFILE_MANAGE = self::PROFILE . '/' . self::POSTFIX_PROFILE_MANAGE;

    const PR_PAGE = 'pr';
    const POSTFIX_PR_PAGE_NEWS = 'hir';

    /**
     *
     * Based on the `yii\helpers\Url::to() method`
     *
     * Below are some examples of using this method:
     *
     * ```php
     * // http://myproject.hu/bejelentesek
     * Link::to(Link::REPORTS)
     *
     * // http://myproject.hu/bejelentesek/budapest/ix-kerulet
     * Link::to([Link::REPORTS, 'budapest', 'ix-kerulet'])
     *
     * // http://myproject.hu/bejelentesek/budapest/ix-kerulet?status=3
     * Link::to([Link::REPORTS, 'budapest', 'ix-kerulet'], ['status' => 3])
     *
     * // http://myproject.hu/bejelentesek/budapest/ix-kerulet?status=3#comments
     * Link::to([Link::REPORTS, 'budapest', 'ix-kerulet'], ['status' => 3, '#' => 'comments'])
     *
     * // http://myproject.hu/bejelentesek/budapest/54/elhagyott-auto
     * Link::to([Link::REPORTS, 'budapest', 54, 'elhagyott-auto'])
     * ```
     *
     * @param array $routeItems
     * @param array $queryParams
     * @param bool $scheme
     * @return string
     */
    public static function to($routeItems = [], $queryParams = [], $scheme = true)
    {
        $fullRoute = '/' . implode('/', (array)$routeItems);

        return Url::to(array_merge([$fullRoute], $queryParams), $scheme);
    }

    /**
     * @param $type
     *
     * @return bool|string
     */
    public static function convertTo($type)
    {
        if (!in_array($type, [self::MAP, self::REPORTS])) {
            return false;
        }

        return static::to([
            $type,
            \Yii::$app->request->get('citySlug'),
        ], ['status' => \Yii::$app->request->get('status')]);
    }
}
