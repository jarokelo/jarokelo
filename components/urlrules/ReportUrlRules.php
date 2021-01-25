<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use app\models\db\Report;
use yii\web\GroupUrlRule;

class ReportUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = false;

    public $routePrefix = false;

    public $rules = [
        [
            'pattern' => Link::ANONYMOUS_REPORT,
            'route' => 'report/create',
            'defaults' => ['confirmedAnonymous' => 1],
        ],

        Link::CREATE_REPORT . '/' . self::DRAFT_SLUG                  => 'report/create',
        Link::CREATE_REPORT . '/' . Link::CREATE_REPORT_SUCCESS       => 'report/success',
        Link::CREATE_REPORT . '/' . self::CITY_SLUG                   => 'report/create',
        Link::CREATE_REPORT . '/<prPageId:\d+>'                       => 'report/create',
        Link::CREATE_REPORT                                           => 'report/create',

        Link::MAP . '/' . self::CITY_SLUG . '/' . self::DISTRICT_SLUG => 'report/map',
        Link::MAP . '/' . self::CITY_SLUG                             => 'report/map',
        Link::MAP                                                     => 'report/map',

        'report/video/embed/<type:\w+>/<id:\S+>'                      => 'report/video-embed',
        'report/video/embed'                                          => 'report/video-embed',
        'report/<id:\d+>'                                             => 'report/report',
    ];
}
