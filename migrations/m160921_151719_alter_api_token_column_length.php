<?php

use yii\db\Migration;

class m160921_151719_alter_api_token_column_length extends Migration
{
    public function up()
    {
        $this->alterColumn('user', 'api_token', $this->string(64));
    }

    public function down()
    {
        $this->alterColumn('user', 'api_token', $this->string(20));
    }
}
