<?php

use yii\db\Migration;

class m160816_104437_add_auth_token_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'auth_token', $this->string(20)->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('user', 'auth_token');
    }
}
