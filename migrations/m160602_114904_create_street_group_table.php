<?php

use yii\db\Migration;

class m160602_114904_create_street_group_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('street_group', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string()->unique()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('street_group__street', [
            'street_group_id' => $this->bigInteger()->notNull(),
            'street_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('street_group__street_sgid', 'street_group__street', 'street_group_id');
        $this->createIndex('street_group__street_street_id', 'street_group__street', 'street_id');

        $this->addForeignKey('street_group__street_FK_sgid', 'street_group__street', 'street_group_id', 'street_group', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('street_group__street_FK_street_id', 'street_group__street', 'street_id', 'street', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('street_group__street_FK_sgid', 'street_group__street');
        $this->dropForeignKey('street_group__street_FK_street_id', 'street_group__street');

        $this->dropIndex('street_group__street_sgid', 'street_group__street');
        $this->dropIndex('street_group__street_street_id', 'street_group__street');

        $this->dropTable('street_group__street');
        $this->dropTable('street_group');
    }
}
