<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;
use app\models\db\CustomForm;

/**
 *
 */
class m210704_133833_create_table_custom_form_relations extends Migration
{
    const TABLE_NAME = 'custom_form_relations';
    const FK_1 = 'custom_form_relation_custom_form_id';

    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'custom_form_id' => $this->integer()->unsigned()->notNull(),
                'type' => "ENUM('report_category', 'report_taxonomy') NOT NULL",
                'entity_id' => $this->integer()->notNull()->unsigned(),
                'priority' => $this->integer()->notNull()->unsigned(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->addForeignKey(
            self::FK_1,
            self::TABLE_NAME,
            'custom_form_id',
            CustomForm::tableName(),
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey(self::FK_1, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
