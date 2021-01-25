<?php

use yii\db\Migration;

class m160503_153033_add_column_report_activity_id_to_report_attachment_and_report_attachment_original extends Migration
{
    public function up()
    {
        $this->addColumn('report_attachment', 'report_activity_id', $this->bigInteger());
        $this->createIndex('report_attachment_index_report_activity', 'report_attachment', 'report_activity_id');
        $this->addForeignKey('report_attachment_FK_report_activity', 'report_attachment', 'report_activity_id', 'report_activity', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('report_attachment_FK_report_activity', 'report_attachment');
        $this->dropIndex('report_attachment_index_report_activity', 'report_attachment');
        $this->dropColumn('report_attachment', 'report_activity_id');
    }
}
