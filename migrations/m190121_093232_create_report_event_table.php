<?php

use yii\db\Migration;
use app\models\db\ReportEvent;

/**
 * Handles the creation of table `report_event`.
 */
class m190121_093232_create_report_event_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('report_event', [
            'id' => $this->primaryKey(),
            'report_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'source' => $this->smallInteger()->notNull()->unsigned(),
        ]);

        $this->createIndex(
            'report_event_report_id',
            ReportEvent::tableName(),
            'report_id',
            true
        );

        $this->addForeignKey(
            'report_event_FK_report',
            ReportEvent::tableName(),
            'report_id',
            'report',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('report_event_FK_report', ReportEvent::tableName());
        $this->dropIndex('report_event_report_id', ReportEvent::tableName());
        $this->dropTable('report_event');
    }
}
