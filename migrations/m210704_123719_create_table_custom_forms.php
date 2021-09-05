<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Class m210704_123719_create_table_custom_forms
 */
class m210704_123719_create_table_custom_forms extends Migration
{
    const TABLE_NAME = 'custom_forms';
    const INDEX_CUSTOM_FORM = 'custom_forms_index_unique_name';

    const INDEX_CREATED_BY = 'custom_forms_index_created_by';
    const INDEX_UPDATED_BY = 'custom_forms_index_updated_by';
    const FK_CREATED_BY = 'custom_forms_created_by_fk_admin_id';
    const FK_UPDATED_BY = 'custom_forms_updated_by_fk_admin_id';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->unsigned()->notNull(),
                'name' => $this->string(255)->notNull(),
                'description' => $this->text()->null(),
                'custom_questions' => $this->text()->notNull(),
                'status' => $this->boolean()->defaultValue(10),
                'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
                'created_by' => $this->bigInteger(),
                'updated_by' => $this->bigInteger(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->createIndex(self::INDEX_CUSTOM_FORM, self::TABLE_NAME, 'name', true);
        $this->createIndex(self::INDEX_CREATED_BY, self::TABLE_NAME, 'created_by');
        $this->createIndex(self::INDEX_UPDATED_BY, self::TABLE_NAME, 'updated_by');
        $this->addForeignKey(self::FK_CREATED_BY, self::TABLE_NAME, 'created_by', 'admin', 'id');
        $this->addForeignKey(self::FK_UPDATED_BY, self::TABLE_NAME, 'updated_by', 'admin', 'id');
    }

    public function down()
    {
        $this->dropIndex(self::INDEX_CUSTOM_FORM, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_CREATED_BY, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_UPDATED_BY, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_CREATED_BY, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_UPDATED_BY, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
