<?php

use yii\db\Migration;

class m160708_155931_alter_table_report_make_some_fields_nullable extends Migration
{
    const TABLE_NAME = 'report';

    public function up()
    {
        $this->alterColumn(self::TABLE_NAME, 'category', $this->string());
        $this->alterColumn(self::TABLE_NAME, 'status', $this->smallInteger()->unsigned()->defaultValue(1));
        $this->alterColumn(self::TABLE_NAME, 'user_location', $this->string());
        $this->alterColumn(self::TABLE_NAME, 'latitude', $this->decimal(10, 8));
        $this->alterColumn(self::TABLE_NAME, 'longitude', $this->decimal(11, 8));
        $this->alterColumn(self::TABLE_NAME, 'zoom', $this->smallInteger()->unsigned()->defaultValue(5));
    }

    public function down()
    {
        $this->alterColumn(self::TABLE_NAME, 'category', $this->string()->notNull());
        $this->alterColumn(self::TABLE_NAME, 'status', $this->smallInteger()->unsigned()->defaultValue(1)->notNull());
        $this->alterColumn(self::TABLE_NAME, 'user_location', $this->string()->notNull());
        $this->alterColumn(self::TABLE_NAME, 'latitude', $this->decimal(10, 8)->notNull());
        $this->alterColumn(self::TABLE_NAME, 'longitude', $this->decimal(11, 8)->notNull());
        $this->alterColumn(self::TABLE_NAME, 'zoom', $this->smallInteger()->unsigned()->defaultValue(5)->notNull());
    }
}
