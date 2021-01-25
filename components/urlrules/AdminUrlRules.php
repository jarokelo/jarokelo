<?php

namespace app\components\urlrules;

use app\components\helpers\Link;
use yii\web\GroupUrlRule;

class AdminUrlRules extends GroupUrlRule
{
    public $prefix = Link::ADMIN;

    public $routePrefix = 'admin';

    public $rules = [
        ''                                               => 'task/index',
        // Auth
        'login'                                          => 'auth/login',
        'logout'                                         => 'auth/logout',

        // Admins
        'administrators'                                 => 'admin/index',
        'administrator/new'                              => 'admin/create',
        'administrator/<id:\d+>'                         => 'admin/update',
        'administrator/<id:\d+>/permission'              => 'admin/permission',
        'administrator/<id:\d+>/delete'                  => 'admin/delete',
        'profile'                                        => 'admin/profile',
        'password'                                       => 'admin/password',
        'delete/<id:\d+>'                                => 'admin/delete',
        'restore/<id:\d+>'                               => 'admin/restore',

        // Institutions
        'institutions'                                   => 'institution/index',
        'institution/new'                                => 'institution/create',
        'institution/<id:\d+>'                           => 'institution/update',
        'institution/<id:\d+>/delete'                    => 'institution/delete',
        'institution/<id:\d+>/contact/<cid:\d+>'         => 'institution/contact',
        'institution/<id:\d+>/contact'                   => 'institution/contact',
        'institution/<id:\d+>/contact/<cid:\d+>/delete'  => 'institution/delete-contact',
        'institution/<id:\d+|ph>/note'                   => 'institution/note',
        'institution/<id:\d+|ph>/contacts/<rid:\d+>'     => 'institution/contact-list',
        'institution/<id:\d+|ph>/contacts'               => 'institution/contact-list',
        'institution/<id:\d+>/reports'                   => 'report/institution',
        'institution/<id:\d+>/export/<type:(excel|pdf)>' => 'report/institution-export',

        // Cities
        'cities'                                         => 'city/index',
        'city/new'                                       => 'city/create',
        'city/<id:\d+>/<tab:(streets|districts|rules)>'  => 'city/view',
        'city/<id:\d+>'                                  => 'city/view',
        'city/<id:\d+>/delete'                           => 'city/delete',
        'city/<id:\d+>/district/<did:\d+>'               => 'city/district',
        'city/<id:\d+>/district'                         => 'city/district',
        'city/<id:\d+>/district/<did:\d+>/delete'        => 'city/delete-district',
        'city/<id:\d+>/street-list'                      => 'city/streets',
        'city/<id:\d+>/street/<sid:\d+>'                 => 'city/street',
        'city/<id:\d+>/street'                           => 'city/street',
        'city/<id:\d+>/street/<sid:\d+>/delete'          => 'city/delete-street',
        'city/<id:\d+>/rule/<rid:\d+>'                   => 'city/rule',
        'city/<id:\d+>/rule'                             => 'city/rule',
        'city/<id:\d+>/rule/<rid:\d+>/delete'            => 'city/delete-rule',

        // Reports

        'reports'                                        => 'report/index',
        'reports/<action:\w+>'                           => 'report/<action>',
        'reports/<id:\d+>'                               => 'report/view',
        'reports/<id:\d+>/<action:\w+>'                  => 'report/<action>',
        'activity/edit/<id:\d+>'                         => 'report/edit-comment',
        'activity/toggle/<id:\d+>'                       => 'report/toggle-comment',

        // Tasks
        'tasks/<tab:(active|new)>'                       => 'task/index',
        'tasks'                                          => 'task/index',
        'task/<id:\d+>/assign'                           => 'task/assign',

        // Users
        'users'                                          => 'user/index',
        'user/<id:\d+>'                                  => 'user/update',
        'user/api/generate'                              => 'user/api-generate',
        'user/api/revoke'                                => 'user/api-revoke',
        'user/<id:\d+>/reports'                          => 'reports/user',
        'user/<id:\d+>/export/<type:(excel|pdf)>'        => 'reports/user-export',

        // Pr pages
        'pr-pages'                                       => 'pr-page/index',
        'pr-page/create/<id:\d+>'                        => 'pr-page/create',
        'pr-page/update/<id:\d+>'                        => 'pr-page/update',

        // Pr page news
        'pr-page-news/index/<id:\d+>'                    => 'pr-page-news/index',
        'pr-page-news/create/<id:\d+>'                   => 'pr-page-news/create',
        'pr-page-news/update/<id:\d+>'                   => 'pr-page-news/update',
        'pr-page-news/delete/<id:\d+>'                   => 'pr-page-news/delete',
        'pr-page-news/highlight/<id:\d+>'                => 'pr-page-news/highlight',

        // Map Layer
        'map-layer'                                      => 'map-layer/index',
        'map-layer/create/<id:\d+>'                      => 'map-layer/create',
        'map-layer/update/<id:\d+>'                      => 'map-layer/update',
        'map-layer/delete/<id:\d+>'                      => 'map-layer/delete',

        // Progress
        'progress'                                       => 'progress/index',
        'progress/create'                                => 'progress/create',

        '<controller:\w+>/<action:\w+>'                  => '<controller>/<action>',
    ];
}
