<?php

use yii\db\Migration;

class m160802_172522_rename_report_activity_comment_shown extends Migration
{
    public function up()
    {
        $this->renameColumn('report_activity', 'comment_shown', 'visible');
    }

    public function down()
    {
        $this->renameColumn('report_activity', 'visible', 'comment_shown');
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
