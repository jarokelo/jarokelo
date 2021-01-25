# Jarokelo.hu - based on Yii 2.0 Framework

[![Build Status](https://travis-ci.org/jarokelo/jarokelo2.0.svg?branch=master)](https://travis-ci.org/jarokelo/jarokelo2.0)

## REQUIREMENTS

The minimum requirement for this application template is that your Web server supports PHP 5.4.0.

## Setting up the development environment

First time to acquire the repository.

```sh
git clone --config core.autocrlf=false git@github.com:jarokelo/jarokelo2.0.git
```

TODO: Consider .gitattributes in the repository itself.

Execute the below commands:

```sh
./compose up -d  # bring up the services
./compose shell ./install  # execute inside the container
./compose shell ./yii migrate up --interactive=0  # create database
./compose ports  # to list ports of services
```

After these, the Járókelő application should be available by default at <https://localhost:11111/>

SSL cert needs exception, see below for more details.

## DIRECTORY STRUCTURE

~~~text
assets/             contains assets definition
commands/           contains console commands (controllers)
components/         contains custom helper classes, widgets, components
config/             contains application configurations
controllers/        contains Web controller classes
mail/               contains view files for e-mails
models/             contains model classes
modules/            contains admin and api modules
runtime/            contains files generated during runtime
tests/              contains various tests for the basic application
vendor/             contains dependent 3rd-party packages
views/              contains view files for the Web application
web/                contains the entry script and Web resources
~~~

## DOCKER

### Docker explanation

Before going on, please read:
<https://docs.docker.com/get-started/>

## Commands

| Command                          | Result                                            |
| -------------------------------- | ------------------------------------------------- |
| ./compose up                     | Start all containers (ctrl-c to stop)             |
| ./compose up -d                  | Start all containers in background                |
| ./compose up --no-abort          | Start all containers, but don't exit if one stops |
| ./compose up --build             | Rebuild images and start all containers (warning: old images need to be manually removed) |
| ./compose stop                   | Stop all containers                                      |
| ./compose down                   | Delete all containers and unnamed volumes. Named volumes (e.g. database volume) will not be deleted. |
| ./compose down --keep            | Stop containers without deleting them                    |
| ./compose ports                  | Find out ports assigned to services                      |
| ./compose shell                  | Start a shell as www-data user                           |
| ./compose shell command arg      | Execute command in docker shell as www-data              |
| ./compose shell --root [command] | Start a shell or execute command as root                 |
| ./compose run command arg        | Run command in temporary container as www-data           |
| ./compose run --root command arg | Run command in temporary container as root               |
| ./compose pull                   | Pull all images                                          |
| mysql -h 0.0.0.0 -P &#96;docker-compose port db 3306 &#124; cut -d: -f2 -u php -p&#96; | connect to database server |

`./compose up` will reuse named volumes, but not unnamed volumes.\
Do not use `docker-compose up`; the compose file needs environment variables that are set by `./compose up`.\
Do not use `docker-compose down`; it leaves unnamed volumes dangling.\
Do not use `docker-compose down -v`; it deletes named volumes too.

Use `./compose shell` to run shell commands inside the container. `npm` and `gulp` commands must be executed as `www-data` in the container.

### Customizing ports

Rename `docker-compose.override.example` to `docker-compose.override.yml`
to customize the exposed ports locally (this file is ignored by git).

### Https

To use https with docker:

1. import the server certificate in your browser from `docker/server.crt`
2. get the ssl port: `docker-compose port web 443`
3. use 127.0.0.1 in the browser: <https://127.0.0.1:*port*>

## Windows

> obsolete because of the clone config parameter
>
> Make sure git does **not** convert LF to CRLF on checkout:
>
> git config --global core.autocrlf input

### Using Docker Toolbox/Kitematic/Virtualbox

Once the VM is created stop it and add the containing directory to be able to mount the repository in the container.

Example:
Git repository is at `D:\devel\jarokelo`.\
Add shared directory `D:\devel` named as `d/devel`.\
Restart the VM.

Port forwarding could be needed in case some services are not available from the Windows host.\
Set on the Virtualbox GUI the port forwards for 127.0.0.1:11111 to the guest 11111. Adjust the port if customization was done elsewhere.

### Docker Quickstart Terminal

Use Docker Quickstart Terminal to run shell commands, or add the following to `~/.profile` and use Git Bash:

~~~sh
docker() {
    if [ -t 1 ] && [ -t 0 ]; then
        winpty docker "$@"
    else
        "$(which docker)" "$@"
    fi
}
export -f docker
~~~

If using docker-machine, run `eval $(docker-machine env)` to set up the environment.

## Docker ports

To obtain the running services' port use the `docker-compose port` command.

| Container | Parancs | Leírás |
| --------- | ------- | ------- |
| web | `docker-compose port web 80` | the app itself |
| db | `docker-compose port db 3306` | the database (can be connected from Sequel Pro or Navicat) |
| mailer | `docker-compose port mailer 1080` | shows outgoing emails |
| logio | `docker-compose port logio 28778` | shows logs generated while logio tab is open, tick the left hand side option to show the logs |
| pma | `docker-compose port pma 80` | phpmyadmin |

## DEPLOYMENT

We use [Deployer](http://deployer.org/) for deployment. After cloning the repository, please modify `deploy.php` to your needs.

You need to obtain a Sentry token to make a deployment.
It can be found in the following link. Though you will need access for the Sentry itself. In terms of these credentials ask help from the team.

https://sentry.io/settings/account/api/auth-tokens/

Or ask the token itself via LastPass.

This auth token has **no expiration date**. Unless it's not revoked it will not expire.

Once you received a sentry token, create a `.sentry_token` file in the project's root folder, and insert here the copied token as plain text.

After correctly setting up, you can deploy the code to staging with this command:

```sh
./dep deploy staging
```

And to production:

```sh
./dep deploy production
```

## Cronjobs

The following jobs should be set for `www-data` user

```sh
* * * * * cd /data/apache/htdocs/jarokelo.hu/current && ./yii cron/cache-user-ranks
*/5 * * * * cd /data/apache/htdocs/jarokelo.hu/current && /usr/local/bin/solo -port=30000 ./yii cron/index
```

## Admin web interface

<http://localhost:11111/citizen>

## Development

### Url helpers

For maintainable urls, the language specific url parts should be placed in the `app\components\helpers\Link` file.
Then you can generate consistent urls in the whole application by the `Link::to()` method, which is very similar to the `\yii\helpers\Url::to()` method.
See the examples in the docblock of the `Link::to()` method.
After you translated the constant values to your language, then the urls will be updated everywhere.
If you got 404 error message, you should run the following command, in the CLI to flush cache:

```sh
./yii cache/flush-all
```

## Websocket

The admin page uses Websocket to disable simultaneous report editing. In order to enable this function, please run the following command (after logging in to the container with the `./compose shell` script):

```sh
./yii websocket-server
```

## SVG icons

Icons are stored in the `asset/main/src/svg` folder. If you'd like to add/remove/modify them, please run the `gulp svg-sprite` command. You can insert the SVGs into any view with the [SVG Helper](app\components\helpers).

## NPM Scripts

The following commands are available:

| Command                   | Result                    |
| ------------------------- | ------------------------- |
| `npm run set-environment` | choose environment        |
| `npm run set-email`       | set email address         |
| `npm run set-sentry`      | set sentry configurations |
| `npm run refresh-hooks`   | refresh git hooks         |
| `npm run set-testroot`    | set test root (see below) |

### Setting up test environment

To be able to run the tests, you have to set up the base URL of your application with this command:

```sh
npm run set-testroot
```

Enter the path of your application's web root without the domain and without the "web" directory.
For example, if your application can be reached at "<http://dev.project.hu/me/yii2/basic/web>", then enter "me/yii2/basic".

To change the domain, edit `/tests/codeception/acceptance.suite.yml` and `/tests/config.json`.

To use WebDriver instead of PhpBrowser, edit `/tests/codeception/acceptance.suite.yml`.

To change the Selenium server, edit `/tests/config.json` and `/tests/codeception/acceptance.suite.yml`.

You can now run the tests with the `gulp test` command.

#### Local acceptance testing with Selenium

The local config is generated during some npm/gulp/etc task.
If you would like to run the acceptance tests, you have to configure Codeception.
Run the below command to generate the local (git ignored) config files. (if they exist, delete them)

~~~sh
php testsetup.php noreply@jarokelo.hu "testweb/index-test.php" http://web:8080/testweb/index-test.php selenium
~~~

Then in the web container you can check and run the tests:

~~~sh
gulp test
~~~

Alternatively to check for missing acceptance test parts, run

~~~sh
php vendor/bin/codecept -c tests/codeception.yml dry-run acceptance
~~~

Running only the acceptance tests:

~~~sh
php vendor/bin/codecept -c tests/codeception.yml run acceptance
~~~

If receive errors on the environment, use `run -vvv` for extra debug levels.

## GULP Correct Readme

The following commands are available:

| Command   | Result                                 |
| --------- | -------------------------------------- |
| `phpcs`   | run php codesniffer on source code     |
| `phpcbf`  | run php code beautifier on source code |
| `watch`   | watch scss files for changes and regenerate css files; starts a livereload server on a random port |
| `build`   | generate minified js, css, optimize images and copy fonts; will run jshint and abort if it fails   |
| `compile` | generate minified js, css, optimize images and copy fonts; will run jshint and abort if it fails   |

To use the livereload server over ssh, forward the random port selected by the watch command to localhost:35729, e.g.:

```bash
ssh -L 35729:localhost:39307 dev.project.hu
```

Some directories are excluded from the `phpcs` task (views, migrations, configs etc.). Check the gulpfile for details.

## ASSETS

Asset bundles should extend from `\app\assets\AssetBundle` and should be placed in `app\assets` or the module's assets directory.
The assets directory should contain a `bundles.php` file, which should return an array of classnames of all asset bundles.
The build process will build these assets.

In the development environment `devPath` is used as the base path for publishing assets; `distPath` is used in production, and
the build process will output files there.

Any array elements in `devJs` will be combined and minified into the js file specified by the element's key.

If `scssPath` is specified, all files found inside that directory will be compiled to a file with the same name under the css diretory.

If `imgPath` is specified, all files found inside that directory will be optimized and copied to a directory with the same name under the dist path.

If `fontPath` is specified, all files found inside that directory will be copied to a directory with the same name under the dist path.

To get the baseUrl of an asset bundle, use the baseUrl property of the registered asset instance:

```php
$assetUrl = AppAsset::register($this)->baseUrl;
```

## Development Workflow for Contributors

### initial setup

 1. clone the repository to your computer
 2. `cd` to the newly cloned folder
 3. run `./compose up -d` to start the docker machine
    - if the compose up failed, then you should pull the last failed package like `docker pull jarokelo/php-apache`
 4. run `./compose shell` to log in to the docker machine
 5. run `./install` to install composer and npm packages
 6. run `./yii migrate up` to create tables
 7. open a new terminal widow/tab
 8. in the new window run `./compose ports` to get the ports where the apps are running
    - web: <https://localhost:11111>
    - db: port:33333
    - mail catcher: <http://localhost:44444>
    - phpmyadmin: <http://localhost:22222>
 9. open <https://localhost:11111> in your browser

## Frontend Development

### SCSS

You need to run `gulp watch`, which will compile the scss files on change to a css file.

The admin site based on bootstrap3. You can add new style rules to the `modules/admin/assets/main/src/scss` path.

The public site has a custom style. You will find these rules at `assets/main/src/sass`

### JavaScript

Public site's JS files: `assets/main/src/js`
Admin site's JS files: `modules/admin/assets/main/src/js`

The development environment uses files from `src` folder.
Before you deploy to staging or production environment, you need to compile/build the js/css files into 1 minified file with the `gulp compile` or `gulp build` commands.

Note: the `gulp compile` or `gulp build` commands does not compiles the sccs files into css files.
You need to run `gulp watch`. After it finished, then hit `CTRL + C` to stop it, then run `gulp compile`.

We commit the result of the compile command in a separate commit.

### third party assets

If you want to use a 3rd party package, then you should require it from bower through composer. Then you should add the files into the `BowerAsset` bundle of the admin or public site.
Read more about it here: [Yii 2.0 Asset Management](http://www.yiiframework.com/doc-2.0/guide-structure-assets.html#bower-npm-assets)

## Branching

We are working with feature branches and pull requests.
If the issue's number is #3 and the name of the issue is `Add last page link to report listing` then you should name your branch like `3-add-last-page-link-to-report-listing`.

To reference the commits in the issue, ensure every commit message contain a reference in the `#<issue number>` format.

The `master` branch is on the staging server.
The `release` branch is on the production server.

To release the state of the branch you are (this should be the master branch) you need to run `./release.sh` then the actual branch will be merged to the release branch and pushed to repo.
