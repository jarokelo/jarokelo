<?php
// @codingStandardsIgnoreStart
class testsetup
// @codingStandardsIgnoreEnd
{
    protected static $copyConfig = [
        'tests/codeception.yml.sample' => 'tests/codeception.yml',
        'tests/codeception/acceptance.suite.sample' => 'tests/codeception/acceptance.suite.yml',
        'config/development/common.local.example' => 'config/development/common.local.php',
        'tests/config.json.sample' => 'tests/config.json',
    ];

    public static function run($args)
    {
        if (!is_array($args) || count($args) < 3) {
            echo "Missing parameters\n";
            return;
        }

        $email = $args[1];
        $testroot = '/' . trim($args[2], '/') . '/testweb/index-test.php';
        $frontendtestroot = '/' . trim($args[2], '/') . '/tests/web/index-test.php';
        $testhost = isset($args[3]) && trim($args[3]) !== '' ? $args[3] : 'http://localhost:8080';

        $seleniumHost = isset($args[4]) && trim($args[4]) !== '' ? $args[4] : false;
        $seleniumPort = isset($args[5]) && trim($args[5]) !== '' ? $args[5] : false;

        foreach (static::$copyConfig as $src => $dest) {
            if (!is_file($dest)) {
                copy($src, $dest);
            }
        }

        $contents = file_get_contents('config/development/common.local.php');

        $contents = preg_replace(
            "/'as dryrun' => \[(\s*)'email'\s*=>\s*'[^']*'/",
            "'as dryrun' => [\\1'email' => '$email'",
            $contents
        );

        file_put_contents('config/development/common.local.php', $contents);

        $contents = file_get_contents('tests/codeception.yml');

        $contents = preg_replace(
            '/c3url:.*/',
            "c3url: $testroot",
            $contents
        );

        $contents = preg_replace(
            '/test_entry_url:.*/',
            "test_entry_url: ${testhost}${testroot}",
            $contents
        );

        file_put_contents('tests/codeception.yml', $contents);

        $contents = file_get_contents('tests/codeception/acceptance.suite.yml');

        $contents = preg_replace(
            '/url:.*/',
            "url: $testhost",
            $contents
        );

        if ($seleniumHost !== false) {
            $contents = preg_replace(
                '/host:.*# selenium/',
                "host: $seleniumHost    # selenium",
                $contents
            );
        }

        if ($seleniumPort !== false) {
            $contents = preg_replace(
                '/port:.*# selenium/',
                "port: $seleniumPort    # selenium",
                $contents
            );
        }

        file_put_contents('tests/codeception/acceptance.suite.yml', $contents);

        $contents = file_get_contents('tests/config.json');

        $contents = preg_replace(
            '/"host":.*?(,\s*$|$)/m',
            '"host": "' . $testhost . '"$1',
            $contents
        );

        if ($seleniumHost !== false) {
            $contents = preg_replace(
                '/"seleniumAddress":.*?(,\s*$|$)/m',
                '"seleniumAddress": "http://' . $seleniumHost . '"$1',
                $contents
            );
        }

        if ($seleniumPort !== false) {
            $contents = preg_replace(
                '/"seleniumPort":.*?(,\s*$|$)/m',
                '"seleniumPort": "' . $seleniumPort . '"$1',
                $contents
            );
        }

        $contents = preg_replace(
            '/"mochaPath":.*?(,\s*$|$)/m',
            '"mochaPath": "' . $frontendtestroot . '"$1',
            $contents
        );

        $contents = preg_replace(
            '/"baseUrl":.*?(,\s*$|$)/m',
            '"baseUrl": "' . $testroot . '"$1',
            $contents
        );

        file_put_contents('tests/config.json', $contents);
    }
}

testsetup::run(isset($_SERVER['argv']) ? $_SERVER['argv'] : []);
