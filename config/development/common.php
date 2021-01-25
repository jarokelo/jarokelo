<?php

require(__DIR__ . '/debug.php');

return [
    'components' => [
        'mailer' => [
            'as dryrun' => [
                'class' => 'app\components\MailerDryRun',
                'email' => false,
            ],
        ],
        'sentry' => [
            'enabled' => false,
        ],
        // 'db' => [
        //     'enableSchemaCache' => false,
        // ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],
    ],
    'params' => [
        'webSocketServer' => 'localhost:8606',
        'gmail_client' => [
            'redirect_uri' => 'https://localhost:11111/gmail/store-token',
        ],
        // TODO: remove
        'cache' => [
            'db' => [
                'report' => 600,
                'user' => 600,
                'reportCommentCount' => 600,
                'userRanks' => 60 * 60 * 24, // daily
                'commonStats' => 300,
                'citySlugById' => 300,
                'cityIdBySlug' => 300,
                'generalDbQuery' => 300,
            ],
        ],
        'aws' => [
            's3' => [
                'rootFolder' => 'root/development/report/',
            ],
        ],
    ],
];
