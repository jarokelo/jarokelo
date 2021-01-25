<?php

use yii\db\Migration;

class m160829_103325_is_old_password_flag extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\db\User::tableName(), 'is_old_password', $this->boolean());
        $this->addColumn(\app\models\db\Admin::tableName(), 'is_old_password', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn(\app\models\db\User::tableName(), 'is_old_password');
        $this->dropColumn(\app\models\db\Admin::tableName(), 'is_old_password');
    }
}
