<?php

use yii\db\Migration;

class m160415_101403_create_original_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('report_original', [
            'report_id' => $this->bigInteger(),
            'name' => $this->string()->notNull(),
            'category' => $this->string()->notNull(),
            'description' => $this->text(),
            'user_location' => $this->string()->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(11, 8)->notNull(),
            'zoom' => $this->smallInteger()->notNull()->unsigned()->defaultValue(5),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('report_attachment_original', [
            'report_attachment_id' => $this->bigInteger(),
            'report_id' => $this->bigInteger(),
            'type' => $this->smallInteger()->notNull()->unsigned(),
            'url' => $this->text(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('report_original_index_report', 'report_original', 'report_id');
        $this->createIndex('report_attachment_original_index_report_attachment', 'report_attachment_original', 'report_attachment_id');
        $this->createIndex('report_attachment_original_index_report', 'report_attachment_original', 'report_id');

        $this->addPrimaryKey('report_original_PK', 'report_original', 'report_id');
        $this->addPrimaryKey('report_attachment_original_PK', 'report_attachment_original', 'report_attachment_id');

        $this->addForeignKey('report_original_FK_report', 'report_original', 'report_id', 'report', 'id');
        $this->addForeignKey('report_attachment_original_FK_report_attachment', 'report_attachment_original', 'report_attachment_id', 'report_attachment', 'id');
        $this->addForeignKey('report_attachment_original_FK_report', 'report_attachment_original', 'report_id', 'report', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('report_original_FK_report', 'report_original');
        $this->dropForeignKey('report_attachment_original_FK_report_attachment', 'report_attachment_original');
        $this->dropForeignKey('report_attachment_original_FK_report', 'report_attachment_original');

        $this->dropIndex('report_original_index_report', 'report_original');
        $this->dropIndex('report_attachment_original_index_report_attachment', 'report_attachment_original');
        $this->dropIndex('report_attachment_original_index_report', 'report_attachment_original');

        $this->dropTable('report_original');
        $this->dropTable('report_attachment_original');
    }
}
