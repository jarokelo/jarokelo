<?php

$config = [
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
        ],
        'urlManager' => [
            'cache' => false,
        ],
    ],
    'bootstrap' => [],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'],
        ],
    ],
];

if (YII_ENV !== 'test') {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
