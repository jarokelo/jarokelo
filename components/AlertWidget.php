<?php
/**
 * Created by PhpStorm.
 * User: aborsos
 * Date: 16. 04. 06.
 * Time: 15:38
 */

namespace app\components;

use Yii;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AlertWidget extends Alert
{

    public static function showAlerts()
    {
        echo '<div class="alert-flashes">';
        foreach (Yii::$app->session->getAllFlashes(true) as $class => $messages) {
            $class = $class == 'error' ? 'danger' : $class;
            foreach ((array)$messages as $message) {
                echo Html::tag('div', is_array($message) ? ArrayHelper::getValue($message, 'title') : $message, ['class' => 'alert alert-' . $class, 'role' => 'alert']);
            }
        }
        echo '</div>';
    }
}
