<?php
/**
 * Application configuration shared by all test types
 */

$dbDsn = getenv("CI_DB_DSN") === false ? getenv("TEST_DB_DSN") : getenv("CI_DB_DSN");
$dbUsername = getenv("CI_DB_USERNAME") === false ? getenv("DB_USERNAME") : getenv("CI_DB_USERNAME");
$dbPassword = getenv("CI_DB_PASSWORD") === false ? getenv("DB_PASSWORD") : getenv("CI_DB_PASSWORD");

$dbConfig = [
    'dsn' => 'mysql:host=localhost;dbname=yii2_basic_tests',
];
if ($dbDsn !== false) {
    $dbConfig['dsn'] = $dbDsn;
}
if ($dbUsername !== false) {
    $dbConfig['username'] = $dbUsername;
}
if ($dbPassword !== false) {
    $dbConfig['password'] = $dbPassword;
}

return [
    'components' => [
        'db' => $dbConfig,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
