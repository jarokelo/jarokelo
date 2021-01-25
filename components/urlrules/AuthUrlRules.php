<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class AuthUrlRules extends GroupUrlRule
{
    public $prefix = false;

    public $routePrefix = false;

    public $rules = [
        [
            'pattern' => Link::LOGIN_FROM_NEW_REPORT,
            'route' => 'auth/login',
            'defaults' => ['fromNewReport' => 1],
        ],
        /*[
            'pattern' => Link::AUTH_FACEBOOK,
            'route' => 'auth/auth',
            'defaults' => ['authClient' => 'facebook'],
        ],
        [
            'pattern' => Link::AUTH_GOOGLE,
            'route' => 'auth/auth',
            'defaults' => ['authClient' => 'google'],
        ],*/

        Link::AUTH_LOGIN             => 'auth/login',
        Link::AUTH_PASSWORD_RECOVERY => 'auth/password-recovery',
        Link::AUTH_SET_NEW_PASSWORD  => 'auth/set-new-password',
        Link::AUTH_LOGOUT            => 'auth/logout',
        Link::AUTH_AUTH              => 'auth/auth',
        Link::AUTH_REGISTER          => 'auth/register',
        Link::AUTH_ERROR             => 'auth/error',
    ];
}
