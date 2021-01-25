<?php

use yii\db\Migration;

class m160309_143821_create_city_related_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('rule', [
            'id' => $this->bigPrimaryKey(),
            'city_id' => $this->bigInteger()->notNull(),
            'district_id' => $this->bigInteger(),
            'institution_id' => $this->bigInteger()->notNull(),
            'category' => $this->string(),
            'status' => $this->smallInteger()->notNull()->unsigned(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('rule_contact', [
            'id' => $this->bigPrimaryKey(),
            'rule_id' => $this->bigInteger()->notNull(),
            'contact_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('street', [
            'id' => $this->bigPrimaryKey(),
            'city_id' => $this->bigInteger()->notNull(),
            'district_id' => $this->bigInteger(),
            'name' => $this->string()->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(11, 8)->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('district', [
            'id' => $this->bigPrimaryKey(),
            'city_id' => $this->bigInteger()->notNull(),
            'name' => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('rule_index_city', 'rule', 'city_id');
        $this->createIndex('rule_index_district', 'rule', 'district_id');
        $this->createIndex('rule_index_institution', 'rule', 'institution_id');
        $this->createIndex('rule_contact_index_rule_contact', 'rule_contact', ['rule_id', 'contact_id'], true);
        $this->createIndex('rule_contact_index_contact', 'rule_contact', 'contact_id');
        $this->createIndex('street_index_city_district', 'street', ['city_id', 'district_id']);
        $this->createIndex('street_index_district', 'street', 'district_id');
        $this->createIndex('district_index_city', 'district', 'city_id');

        $this->addForeignKey('rule_FK_city', 'rule', 'city_id', 'city', 'id');
        $this->addForeignKey('rule_FK_district', 'rule', 'district_id', 'district', 'id', 'SET NULL');
        $this->addForeignKey('rule_FK_institution', 'rule', 'institution_id', 'institution', 'id');
        $this->addForeignKey('rule_contact_FK_rule', 'rule_contact', 'rule_id', 'rule', 'id', 'CASCADE');
        $this->addForeignKey('rule_contact_FK_contact', 'rule_contact', 'contact_id', 'contact', 'id', 'CASCADE');
        $this->addForeignKey('street_FK_city', 'street', 'city_id', 'city', 'id');
        $this->addForeignKey('street_FK_district', 'street', 'district_id', 'district', 'id', 'SET NULL');
        $this->addForeignKey('district_FK_city', 'district', 'city_id', 'city', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('rule_FK_city', 'rule');
        $this->dropForeignKey('rule_FK_district', 'rule');
        $this->dropForeignKey('rule_FK_institution', 'rule');
        $this->dropForeignKey('rule_contact_FK_rule', 'rule_contact');
        $this->dropForeignKey('rule_contact_FK_contact', 'rule_contact');
        $this->dropForeignKey('street_FK_city', 'street');
        $this->dropForeignKey('street_FK_district', 'street');
        $this->dropForeignKey('district_FK_city', 'district');

        $this->dropIndex('rule_index_city', 'rule');
        $this->dropIndex('rule_index_district', 'rule');
        $this->dropIndex('rule_index_institution', 'rule');
        $this->dropIndex('rule_contact_index_rule_contact', 'rule_contact');
        $this->dropIndex('rule_contact_index_contact', 'rule_contact');
        $this->dropIndex('street_index_city_district', 'street');
        $this->dropIndex('street_index_district', 'street');
        $this->dropIndex('district_index_city', 'district');

        $this->dropTable('rule');
        $this->dropTable('rule_contact');
        $this->dropTable('street');
        $this->dropTable('district');
    }
}
