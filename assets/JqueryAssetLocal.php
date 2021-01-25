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
class JqueryAssetLocal extends AssetBundle
{
    public $sourcePath = '@bower/jquery/dist';
    public $devJs = ['jquery.js'];
    public $js = ['jquery.min.js'];
}
