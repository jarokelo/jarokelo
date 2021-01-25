<?php

use app\models\db\User;
use yii\helpers\Json;
use yii\web\JsonResponseFormatter;

/* @var \app\models\db\User $model */

    echo json_encode($model->fullData());
?>
