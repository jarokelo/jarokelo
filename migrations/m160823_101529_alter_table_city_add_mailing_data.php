<?php

use yii\db\Migration;

class m160823_101529_alter_table_city_add_mailing_data extends Migration
{
    const TABLE_NAME = 'city';

    public function up()
    {
        $this->addColumn(self::TABLE_NAME, 'email_address', $this->string()->notNull());
        $this->addColumn(self::TABLE_NAME, 'email_password', $this->string());
    }

    public function down()
    {
        $this->dropColumn(self::TABLE_NAME, 'email_address');
        $this->dropColumn(self::TABLE_NAME, 'email_password');
    }
}
