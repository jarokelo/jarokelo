<?php

namespace app\setup;

use yii\helpers\Console;

class Setup
{
    const PATHS_HELP = 'To add paths that the app will write into, edit setup_paths.php and re-run setup.';
    const PATHS_NONE = 'No writable paths configured. Are you sure this is correct?';
    const PATHS_INFO = 'The app may write to the following paths:';

    protected static $sharedWritablePaths = [];
    protected static $otherWritablePaths = [];

    public static function run()
    {
        if (PHP_SAPI == 'cli') {
            static::runCli(isset($_SERVER['argv']) ? $_SERVER['argv'] : []);
        } else {
            static::runWeb();
        }
    }

    protected static function getPostVar($name, $default = null, $strict = false)
    {
        if (!isset($_POST[$name]) || (!$strict && empty($_POST[$name]))) {
            return $default;
        }
        return $_POST[$name];
    }

    protected static function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    public static function runWeb()
    {
        $viewFile = __DIR__ . '/views/index.php';

        $environment = static::getPostVar('environment', static::$defaultEnvironment);
        $serverGroup = static::getPostVar('group', static::$defaultServerGroup, true);

        $errors = [
            'group' => [],
            'environment' => [],
        ];
        $warnings = [
            'group' => [],
            'environment' => [],
        ];
        $writablePaths = false;

        $posted = static::isPost();
        if ($posted) {
            static::writeEnv($environment, $errors['environment'], $warnings['environment']);
            if ($serverGroup !== '') {
                $grp_info = static::getGroupName($serverGroup);
                if ($grp_info === false) {
                    $errors['group'][] = "Group $serverGroup not found";
                } else {
                    $writablePaths = [];
                    static::setupWritablePaths($serverGroup, $errors['group'], $warnings['group'], $writablePaths);
                }
            }
        }

        static::renderViewFile($viewFile, [
            'environments' => static::$environments,
            'serverGroups' => static::$serverGroups,
            'selectedEnvironment' => $environment,
            'selectedGroup' => $serverGroup,
            'errors' => $errors,
            'warnings' => $warnings,
            'posted' => $posted,
            'writablePaths' => $writablePaths,
        ]);
    }

    protected static function colorSupported()
    {
        return Console::streamSupportsAnsiColors(\STDOUT);
    }

    protected static function cliError($s, $newline = true)
    {
        if (static::colorSupported()) {
            echo Console::ansiFormat($s, [Console::FG_RED, Console::BOLD]);
        } else {
            echo $s;
        }
        if ($newline) {
            echo "\n";
        }
    }

    protected static function cliWarning($s, $newline = true)
    {
        if (static::colorSupported()) {
            echo Console::ansiFormat($s, [Console::FG_YELLOW]);
        } else {
            echo $s;
        }
        if ($newline) {
            echo "\n";
        }
    }

    protected static function cliInfo($s, $newline = true)
    {
        if (static::colorSupported()) {
            echo Console::ansiFormat($s, [Console::FG_GREEN]);
        } else {
            echo $s;
        }
        if ($newline) {
            echo "\n";
        }
    }

    public static function runCli($args)
    {
        if (!is_array($args) || count($args) < 2) {
            static::cliError('Missing parameter: environment name');
            return;
        }

        $errors = [];
        $warnings = [];
        $paths = [];

        static::writeEnv($args[1], $errors, $warnings);

        $groupName = count($args) > 2 ? $args[2] : static::$defaultServerGroup;
        $server_group = static::getGroupName($groupName);
        $pathsAttempted = false;
        if ($server_group === false) {
            $errors[] = "Group $groupName not found";
        } else {
            $pathsAttempted = true;
            static::setupWritablePaths($server_group, $errors, $warnings, $paths);
        }

        if (count($errors)) {
            echo "Errors during setup:\n";
        }
        foreach ($errors as $error) {
            static::cliError($error);
        }
        if (count($warnings)) {
            echo "Warnings during setup:\n";
        }
        foreach ($warnings as $warning) {
            static::cliWarning($warning);
        }

        $successPaths = array_keys(array_filter($paths));

        if ($pathsAttempted) {
            if (count($successPaths)) {
                echo self::PATHS_INFO;
                echo "\n";
                foreach ($successPaths as $path) {
                    echo "\t";
                    static::cliInfo($path);
                }
            } elseif (!count($paths)) {
                static::cliWarning(self::PATHS_NONE);
            }
            echo self::PATHS_HELP;
            echo "\n";
        }
    }

    protected static function getGroupName($grpName)
    {
        $grp_info = @posix_getgrnam($grpName);
        if ($grp_info === false) {
            return false;
        }
        return $grp_info['name'];
    }

    protected static function writeEnv($env, &$errors = [], &$warnings = [])
    {
        $baseDir = static::getBaseDir();

        $confDir = $baseDir . '/config';
        $env_config = $confDir . '/' . $env;

        if (!@is_writable($confDir)) {
            $errors[] = 'Configuration dir does not exist or is not writable';
            return;
        }

        if (!@is_dir($env_config)) {
            if (!@mkdir($env_config)) {
                $warnings[] = "No configuration found for environment \"$env\", configuration directory could not be created";
            } else {
                $warnings[] = "No configuration found for environment \"$env\", configuration directory has been created";
            }
        }
        static::generateCookieValidationKey($env_config . '/web.local.php', $errors, $warnings);

        if (@file_put_contents($confDir . '/ENV', $env) === false) {
            $errors[] = 'Could not write environment to config/ENV';
        }
    }

    protected static function setupWritablePaths($server_group, &$errors = [], &$warnings = [], &$infos = [])
    {
        $baseDir = static::getBaseDir();

        $paths = [];
        // only the shared paths should be logged to the console
        foreach (static::$sharedWritablePaths as $path) {
            $paths[$path] = true;
        }
        foreach (static::$otherWritablePaths as $path) {
            $paths[$path] = false;
        }

        foreach ($paths as $path => $log) {
            if ($log) {
                $infos[$path] = false;
            }
            if (!@is_dir($baseDir . '/' . $path)) {
                if (!@mkdir($baseDir . '/' . $path, 02775)) {
                    $errors[] = "Failed to create $path";
                    continue;
                }
            }
            $hasErrors = false;
            if (!@chgrp($baseDir . '/' . $path, $server_group)) {
                $warnings[] = "Failed to chown $path to $server_group";
                $hasErrors = true;
            }
            if (!@chmod($baseDir . '/' . $path, 02775)) {
                $warnings[] = "Failed to make $path writable";
                $hasErrors = true;
            }
            if ($log) {
                $infos[$path] = !$hasErrors;
            }
        }
    }

    protected static function generateCookieValidationKey($configFile, &$errors = [], &$warnings = [])
    {
        //$key = static::generateRandomString();
        $security = new \yii\base\Security();
        $key = $security->generateRandomString();
        if (@is_file($configFile)) {
            $count = 0;
            $contents = file_get_contents($configFile);
            if (preg_match('/(("|\')cookieValidationKey("|\')\s*=>\s*)("|\')[^"\']+("|\')/', $contents) !== 1) {
                $content = preg_replace('/(("|\')cookieValidationKey("|\')\s*=>\s*)(""|\'\')/', "\\1'$key'", $contents, -1, $count);
                if ($count === 0) {
                    $warnings[] = "Failed to set cookie validation key in $configFile";
                    return;
                } elseif ($count !== 1) {
                    $warnings[] = "More than one cookie validation key found in $configFile";
                }
            } else {
                return;
            }
        } else {
            $content =
                "<?php\n"
                . "return [\n"
                . "    'components' => [\n"
                . "        'request' => [\n"
                . "            'cookieValidationKey' => " . var_export($key, true) . ",\n"
                . "        ],\n"
                . "    ],\n"
                . "];\n";
        }
        if (@file_put_contents($configFile, $content) === false) {
            $errors[] = "Failed to save cookie validation key to $configFile";
        }
    }

    /*protected static function generateRandomString()
    {
        if (!extension_loaded('mcrypt')) {
            throw new \Exception('The mcrypt PHP extension is required by Yii2.');
        }
        $length = 32;
        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
    }*/

    protected static function getBaseDir()
    {
        if (static::$baseDir === null) {
            throw new \Exception('baseDir is null');
        }
        return static::$baseDir;
    }

    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     * @param string $_viewFile_ view file
     * @param array $_data_ data to be extracted and made available to the view file
     * @param boolean $_return_ whether the rendering result should be returned as a string
     * @return string the rendering result. Null if the rendering result is not required.
     */
    protected static function renderViewFile($_viewFile_, $_data_ = null, $_return_ = false)
    {
        // we use special variable names here to avoid conflict when extracting data
        if (is_array($_data_)) {
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        } else {
            $data = $_data_;
        }
        if ($_return_) {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        } else {
            require($_viewFile_);
        }
    }

    protected static function encode($content)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
