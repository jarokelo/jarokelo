<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Class m210701_130733_create_table_report_report_taxonomy_connection
 */
class m210701_130733_create_table_report_taxonomy_relation extends Migration
{
    const TABLE_NAME = 'report_taxonomy_relation';
    const FK_1 = 'report_taxonomy_relation_report_category_id';
    const FK_2 = 'report_taxonomy_relation_report_taxonomy_id';
    const INDEX = self::TABLE_NAME . '__report_category_id_report_taxonomy_id';

    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'report_category_id' => $this->integer(11)->notNull(),
            'report_taxonomy_id' => $this->integer()->notNull()->unsigned(),
        ], MigrationHelper::TABLE_OPTIONS);

        $this->createIndex(self::INDEX, self::TABLE_NAME, ['report_category_id', 'report_taxonomy_id'], true);
        $this->addForeignKey(self::FK_1, self::TABLE_NAME, 'report_category_id', 'report_category', 'id');
        $this->addForeignKey(
            self::FK_2,
            self::TABLE_NAME,
            'report_taxonomy_id',
            'report_taxonomy',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey(self::FK_2, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_1, self::TABLE_NAME);
        $this->dropIndex(self::INDEX, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
