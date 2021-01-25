<?php
/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property app\components\JarokeloMailer
 * @property yii\image\ImageDriver $image
 * @property app\components\Preload $preload
 * @property app\components\sentry\SentryComponent $sentry
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property \app\components\UserComponent $user The user component. This property is read-only. Extended component.
 * @property \app\modules\api\components\AppUserComponent $appUser The api user component for app
 * @property \app\components\Response $response
 * @property \app\components\FeedDriver $feed
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 */
class ConsoleApplication extends yii\console\Application
{
}
