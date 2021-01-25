<?php

use yii\db\Migration;

class m160824_153117_fix_default_user_api_rate_limit extends Migration
{
    public function up()
    {
        $this->update(\app\models\db\User::tableName(), ['api_rate_limit' => 1]);
    }

    public function down()
    {
        return true;
    }
}
