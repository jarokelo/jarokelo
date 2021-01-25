<?php

use yii\db\Migration;

class m160802_104134_add_token_field_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'api_token', $this->string(20)->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('user', 'api_token');
    }
}
