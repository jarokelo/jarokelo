<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Class m210627_153108_create_table_report_taxonomy
 */
class m210627_153108_create_table_report_taxonomy extends Migration
{
    const TABLE_REPORT_TAXONOMY = 'report_taxonomy';

    public function up()
    {
        $this->createTable(self::TABLE_REPORT_TAXONOMY, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'name' => $this->string(255)->null(),
            'is_active' => $this->boolean()->defaultValue(1),
        ], MigrationHelper::TABLE_OPTIONS);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_REPORT_TAXONOMY);
    }
}
