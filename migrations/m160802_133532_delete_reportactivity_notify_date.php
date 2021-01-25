<?php

use yii\db\Migration;

class m160802_133532_delete_reportactivity_notify_date extends Migration
{
    public function up()
    {
        $this->dropColumn('report_activity', 'notify_date');
    }

    public function down()
    {
        $this->addColumn('report_activity', 'notify_date', $this->bigInteger()->unsigned());
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
