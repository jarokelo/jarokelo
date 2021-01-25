<?php

use yii\db\Migration;

class m160304_162541_create_city_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('city', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string()->notNull(),
            'has_districts' => $this->boolean()->notNull()->defaultValue(1),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('admin_city', [
            'admin_id' => $this->bigInteger()->notNull(),
            'city_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),

            'PRIMARY KEY (`admin_id`, `city_id`)',
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('admin_city_index_city', 'admin_city', 'city_id');

        $this->addForeignKey('admin_city_FK_admin', 'admin_city', 'admin_id', 'admin', 'id');
        $this->addForeignKey('admin_city_FK_city', 'admin_city', 'city_id', 'city', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('admin_city_FK_admin', 'admin_city');
        $this->dropForeignKey('admin_city_FK_city', 'admin_city');

        $this->dropIndex('admin_city_index_city', 'admin_city');

        $this->dropTable('city');
        $this->dropTable('admin_city');
    }
}
