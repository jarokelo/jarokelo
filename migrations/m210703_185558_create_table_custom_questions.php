<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 *
 */
class m210703_185558_create_table_custom_questions extends Migration
{
    const TABLE_NAME = 'custom_questions';
    const INDEX_QUESTION = 'custom_questions_index_unique_question';
    const INDEX_CREATED_BY = 'custom_questions_index_created_by';
    const INDEX_UPDATED_BY = 'custom_questions_index_updated_by';
    const FK_CREATED_BY = 'custom_questions_created_by_fk_admin_id';
    const FK_UPDATED_BY = 'custom_questions_updated_by_fk_admin_id';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned()->notNull(),
                'question' => $this->string(255)->notNull(),
                'description' => $this->text()->null(),
                'status' => $this->boolean()->defaultValue(10),
                'type' => $this->integer()->notNull()->unsigned(),
                'answer_options' => $this->text()->null(),
                'required' => $this->boolean()->notNull()->defaultValue(0),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
                'created_by' => $this->bigInteger(),
                'updated_by' => $this->bigInteger(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->createIndex(self::INDEX_QUESTION, self::TABLE_NAME, 'question', true);
        $this->createIndex(self::INDEX_CREATED_BY, self::TABLE_NAME, 'created_by');
        $this->createIndex(self::INDEX_UPDATED_BY, self::TABLE_NAME, 'updated_by');
        $this->addForeignKey(self::FK_CREATED_BY, self::TABLE_NAME, 'created_by', 'admin', 'id');
        $this->addForeignKey(self::FK_UPDATED_BY, self::TABLE_NAME, 'updated_by', 'admin', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey(self::FK_CREATED_BY, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_UPDATED_BY, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_QUESTION, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_CREATED_BY, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_UPDATED_BY, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
