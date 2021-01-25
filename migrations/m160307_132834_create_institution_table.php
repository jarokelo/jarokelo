<?php

use yii\db\Migration;

class m160307_132834_create_institution_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('institution', [
            'id' => $this->bigPrimaryKey(),
            'city_id' => $this->bigInteger()->notNull(),
            'name' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'note' => $this->text(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('contact', [
            'id' => $this->bigPrimaryKey(),
            'institution_id' => $this->bigInteger()->notNull(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('institution_index_city', 'institution', 'city_id');
        $this->createIndex('institution_index_name', 'institution', 'name');
        $this->createIndex('contact_index_institution', 'contact', 'institution_id');

        $this->addForeignKey('institution_FK_city', 'institution', 'city_id', 'city', 'id');
        $this->addForeignKey('contact_FK_institution', 'contact', 'institution_id', 'institution', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('institution_FK_city', 'institution');
        $this->dropForeignKey('contact_FK_institution', 'contact');

        $this->dropIndex('institution_index_name', 'institution');
        $this->dropIndex('institution_index_city', 'institution');
        $this->dropIndex('contact_index_institution', 'contact');

        $this->dropTable('institution');
        $this->dropTable('contact');
    }
}
