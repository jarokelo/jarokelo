<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use app\models\db\Report;
use yii\web\GroupUrlRule;

class ReportsUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = Link::REPORTS;

    public $routePrefix = false;

    public $rules = [
        [
            'pattern' => Link::POSTFIX_FEATURED . '/' . self::CITY_SLUG . '/' . self::DISTRICT_SLUG,
            'route' => 'report/list',
            'defaults' => [
                'status' => Report::CUSTOM_FILTER_HIGHLIGHTED,
                'citySlug' => '',
                'districtSlug' => '',
            ],
        ],
        [
            'pattern' => Link::POSTFIX_FRESH . '/' . self::CITY_SLUG . '/' . self::DISTRICT_SLUG,
            'route' => 'report/list',
            'defaults' => [
                'status' => Report::CUSTOM_FILTER_FRESH,
                'citySlug' => '',
                'districtSlug' => '',
            ],
        ],
        [
            'pattern' => Link::POSTFIX_NEARBY . '/' . self::CITY_SLUG . '/' . self::DISTRICT_SLUG,
            'route' => 'report/list',
            'defaults' => [
                'status' => Report::CUSTOM_FILTER_NEARBY,
                'citySlug' => '',
                'districtSlug' => '',
            ],
        ],
        [
            'pattern' => self::CITY_SLUG . '/<id:\d+>/' . self::REPORT_SLUG . '/' . Link::POSTFIX_REPORT_EDM,
            'route' => 'report/view',
            'defaults' => [
                'source' => Report::SOURCE_EDM,
            ],
        ],
        [
            'pattern' => self::CITY_SLUG . '/<id:\d+>/' . self::REPORT_SLUG . '/' . Link::POSTFIX_REPORT_PDF,
            'route' => 'report/view',
            'defaults' => [
                'source' => Report::SOURCE_PDF,
            ],
        ],

        Link::POSTFIX_REPORT_COMMENT . '/<id:\d+>'         => 'report/show-comment',

        self::CITY_SLUG . '/<id:\d+>/' . self::REPORT_SLUG => 'report/view',
        self::CITY_SLUG . '/' . self::DISTRICT_SLUG        => 'report/list',
        self::CITY_SLUG                                    => 'report/list',
        ''                                                 => 'report/list',
    ];
}
