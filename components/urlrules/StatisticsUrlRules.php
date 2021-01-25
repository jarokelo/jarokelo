<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class StatisticsUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = Link::STATISTICS;

    public $routePrefix = false;

    public $rules = [
        '' => 'statistics/index',
        Link::POSTFIX_STATISTICS_CITIES . '/' . self::CITY_SLUG => 'statistics/cities',
        Link::POSTFIX_STATISTICS_CITIES => 'statistics/cities',
        Link::POSTFIX_STATISTICS_INSTITUTIONS . '/' . self::INSTITUTION_SLUG => 'statistics/institutions',
        Link::POSTFIX_STATISTICS_INSTITUTIONS => 'statistics/institutions',
        Link::POSTFIX_STATISTICS_USERS . '/' . self::CITY_SLUG => 'statistics/users',
        Link::POSTFIX_STATISTICS_USERS => 'statistics/users',
    ];
}
