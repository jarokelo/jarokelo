'use strict';

var fs = require('fs');
var path = require('path');
var inquirer = require('inquirer');
var Promise = require('pinkie-promise');
var execSync = require('child_process').execSync;
var argv = require('minimist')(process.argv.slice(2));
var config = require('gulp/config.json');
var existsSync = require('npm-scripts/modules/exists-sync');

var dockerized = process.env.DOCKERIZED === 'true';

var getComposerJson = function() {
    return require('composer.json');
};

var getCopyKeepSource = function(obj) {
    var src = obj.src;
    var dest = path.join(obj.dest);

    if (existsSync(dest)) {
        src = dest;
    }

    return src;
};

var setEnvironment = function(environment, apachegrp) {
    apachegrp = !apachegrp && argv.apachegrp ? argv.apachegrp : '';

    try {
        execSync('php setup.php ' + environment + ' ' + apachegrp);
    } catch (e) {
        console.log(e);
    }
};

var setEmail = function(email) {
    var src = getCopyKeepSource(config.copykeep.localemail);
    var file = fs.readFileSync(src, {
        encoding: 'utf8'
    });

    var replacement;
    if (email === false) {
        replacement = 'false';
    } else {
        replacement = "'" + email + "'";
    }

    file = file.replace(/'as dryrun' => \[(\s*)'email'\s*=>\s*'[^']*'/, '\'as dryrun\' => [ \'email\' => ' + replacement);
    fs.writeFileSync(config.copykeep.localemail.dest, file);
};

var setTestroot = function(testroot) {
    var src;
    var file;

    var base = testroot.replace(/^(\/)|(\/)$/g, '').trim();
    if (base !== '') {
        base += '/';
    }

    src = getCopyKeepSource(config.copykeep.testroot);
    file = fs.readFileSync(src, {
        encoding: 'utf8'
    });
    file = file.replace(/c3url:.*/, 'c3url: /' + base + 'testweb/index-test.php');
    file = file.replace(/test_entry_url:.*/, 'test_entry_url: /' + base + 'testweb/index-test.php');
    fs.writeFileSync(config.copykeep.testroot.dest, file);

    src = getCopyKeepSource(config.copykeep.acceptancehost);
    file = fs.readFileSync(src, {
        encoding: 'utf8'
    });

    if (dockerized) {
        file = file.replace(/url:.*/g, 'url: http://web:8080');
        file = file.replace(/host:.*# selenium/, 'host: selenium    # selenium');
    }

    fs.writeFileSync(config.copykeep.acceptancehost.dest, file);

    src = getCopyKeepSource(config.copykeep.jstestconfig);
    file = fs.readFileSync(src, {
        encoding: 'utf8'
    });
    file = file.replace(/"mochaPath":.*?(,\s*$|$)/m, '"mochaPath": "/' + base + 'tests/web/index-test.php"$1');
    file = file.replace(/"baseUrl":.*?(,\s*$|$)/m, '"baseUrl": "/' + base + 'testweb/index-test.php"$1');
    if (dockerized) {
        file = file.replace(/"seleniumAddress":.*?(,\s*$|$)/m, '"seleniumAddress": "http://selenium"$1');
        file = file.replace(/"host":.*?(,\s*$|$)/m, '"host": "http://web:8080"$1');
    }
    fs.writeFileSync(config.copykeep.jstestconfig.dest, file);
};

var setSentry = function(sentryDSN) {
    var indent = function(count) {
        return function(val, index) {
            if (index === 0) {
                return val;
            }
            return '    '.repeat(count) + val;
        };
    };

    var sentryComponent = [
        '\'sentry\' => [',
        '    \'class\' => \'app\\components\\sentry\\SentryComponent\',',
        '    \'dsn\' => \'' + sentryDSN + '\',',
        '    \'environment\' => YII_CONFIG_ENVIRONMENT,',
        '    \'jsNotifier\' => true,',
        '    \'clientOptions\' => [',
        '        \'whitelistUrls\' => [',
        '            //\'http://your-project.staging.hu\',',
        '            //\'https://your-project.hu\',',
        '        ],',
        '    ],',
        '],'
    ].map(indent(2)).join('\n');

    var sentryTarget = [
        '[',
        '    \'class\' => \'app\\components\\sentry\\SentryTarget\',',
        '    \'levels\' => [\'error\', \'warning\'],',
        '    \'except\' => [',
        '        \'yii\\web\\HttpException:404\',',
        '    ],',
        '],'
    ].map(indent(4)).join('\n');

    var sentryComponentDev = '\'sentry\' => [\'enabled\' => false],';

    var src;
    var file;

    src = getCopyKeepSource(config.copykeep.sentryConfig);
    file = fs.readFileSync(src, {
        encoding: 'utf8'
    });
    file = file.replace(/\/\*'sentry' => \[],\*\//, sentryComponent);
    file = file.replace(/\/\*'sentryTarget' => \[],\*\//, sentryTarget);
    fs.writeFileSync(config.copykeep.sentryConfig.dest, file);

    src = getCopyKeepSource(config.copykeep.sentryDevConfig);
    file = fs.readFileSync(src, {
        encoding: 'utf8'
    });
    file = file.replace(/\/\*'sentry' => \['enabled' => false],\*\//, sentryComponentDev);
    fs.writeFileSync(config.copykeep.sentryDevConfig.dest, file);


    var composerJson = getComposerJson();

    var packageJson = require('package.json');

    if (!packageJson.eslintConfig.globals) {
        packageJson.eslintConfig.globals = {};
    }

    packageJson.eslintConfig.globals.Raven = true;

    fs.writeFileSync('package.json', JSON.stringify(packageJson, null, 2));
};

var Setup = {};

Setup.setEnvironment = function() {
    var promise = function(resolve) {
        if (argv.environment) {
            setEnvironment(argv.environment);
            resolve();
        } else if (dockerized && process.env.APPENV) {
            setEnvironment(process.env.APPENV);
            resolve();
        } else {
            var prompt = inquirer.prompt([{
                type: 'list',
                name: 'environment',
                message: 'Select environment',
                choices: [
                    'development',
                    'staging',
                    'production',
                    new inquirer.Separator(),
                    'other'
                ]
            }, {
                type: 'input',
                name: 'environment',
                message: 'Type custom environment',
                when: function(res) {
                    return res.environment === 'other';
                }
            }]);

            prompt.then(function(res) {
                setEnvironment(res.environment);
                resolve();
            });
        }
    };

    return new Promise(promise);
};

Setup.setEmail = function() {
    var promise = function(resolve) {
        if (argv.email) {
            setEmail(argv.email);
            resolve();
        } else if (dockerized) {
            setEmail(false);
            resolve();
        } else {
            var prompt = inquirer.prompt([{
                type: 'input',
                name: 'email',
                message: 'Your email',
                validate: function(res) {
                    if (res === '') {
                        return 'Please enter an email address';
                    }

                    return true;
                }
            }]);

            prompt.then(function(res) {
                setEmail(res.email);
                resolve();
            });
        }
    };

    return new Promise(promise);
};

Setup.setTestroot = function() {
    var promise = function(resolve) {
        if (argv.testroot) {
            setTestroot(argv.testroot);
            resolve();
        } else if (dockerized) {
            // TODO: this is wrong, since DocumentRoot is /var/www/app/web in docker
            setTestroot('/');
            resolve();
        } else {
            var prompt = inquirer.prompt([{
                type: 'input',
                name: 'testroot',
                message: 'Base URL of this app (e.g. "/me/yii2/basic")'
            }]);

            prompt.then(function(res) {
                setTestroot(res.testroot);
                resolve();
            });
        }
    };

    return new Promise(promise);
};

Setup.setSentry = function() {
    var promise = function(resolve) {
        var prompt = inquirer.prompt([{
            type: 'list',
            name: 'sentry',
            message: 'Would you like to use Sentry to catch errors?',
            choices: ['yes', 'no']
        }, {
            type: 'input',
            name: 'sentry',
            message: 'Paste the private DSN',
            when: function(res) {
                if (res.sentry === 'yes') {
                    return true;
                }
                return false;
            }
        }]);

        prompt.then(function(res) {
            if (res.sentry !== 'no') {
                setSentry(res.sentry);
            }
            resolve();
        });
    };

    return new Promise(promise);
};

module.exports = Setup;
