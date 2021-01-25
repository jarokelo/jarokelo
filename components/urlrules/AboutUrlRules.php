<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class AboutUrlRules extends GroupUrlRule implements SlugPatternInterface
{
    public $prefix = Link::ABOUT;

    public $routePrefix = false;

    public $rules = [
        '' => 'about/index',
        Link::POSTFIX_ABOUT_CONTACT => 'about/contact',
        Link::POSTFIX_ABOUT_VOLUNTEER => 'about/volunteer',
        Link::POSTFIX_ABOUT_HOWITWORKS => 'about/how-it-works',
        Link::POSTFIX_ABOUT_TOS => 'about/tos',
        Link::POSTFIX_ABOUT_SUPPORT => 'about/support',
        Link::POSTFIX_ABOUT_BUREAU => 'about/bureau',
        Link::POSTFIX_ABOUT_PARTNERS => 'about/partners',
        Link::POSTFIX_ABOUT_ANNUALREPORTS => 'about/annual-reports',
        Link::POSTFIX_ABOUT_TEAM => 'about/team',
    ];
}
