<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use app\models\db\Report;
use yii\web\GroupUrlRule;

class ProfileUrlRules extends GroupUrlRule
{
    public $prefix = Link::PROFILE;

    public $routePrefix = false;

    public $rules = [
        [
            'pattern' => Link::POSTFIX_PROFILE_DRAFTS,
            'route' => 'profile/index',
            'defaults' => [
                'status' => Report::STATUS_DRAFT,
            ],
        ],
        Link::POSTFIX_PROFILE_MANAGE => 'profile/manage',
        '' => 'profile/index',
    ];
}
