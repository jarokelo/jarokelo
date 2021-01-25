<?php

use yii\db\Migration;

class m160503_153734_drop_column_attachment_id_from_report_activity extends Migration
{
    public function up()
    {
        $this->dropForeignKey('report_activity_FK_attachment', 'report_activity');
        $this->dropIndex('report_activity_index_attachment', 'report_activity');
        $this->dropColumn('report_activity', 'attachment_id');
    }

    public function down()
    {
        $this->addColumn('report_activity', 'attachment_id', $this->bigInteger());
        $this->createIndex('report_activity_index_attachment', 'report_activity', 'attachment_id');
        $this->addForeignKey('report_activity_FK_attachment', 'report_activity', 'attachment_id', 'report_attachment', 'id');
    }
}
