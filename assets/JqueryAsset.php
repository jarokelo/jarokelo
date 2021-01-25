<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

/**
 * This asset bundle provides the [jquery javascript library](http://jquery.com/)
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JqueryAsset extends FallbackAssetBundle
{
    public $sourcePath = null;
    public $devJs = ['//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.js'];
    public $js = ['//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js'];
}
