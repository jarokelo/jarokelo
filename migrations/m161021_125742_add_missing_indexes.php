<?php

use yii\db\Migration;

class m161021_125742_add_missing_indexes extends Migration
{
    public function up()
    {
        $this->createIndex('report_activity_index_is_active_task', \app\models\db\ReportActivity::tableName(), 'is_active_task');
        $this->createIndex('report_index_name', \app\models\db\Report::tableName(), 'name');
        $this->createIndex('report_index_latitude', \app\models\db\Report::tableName(), 'latitude');
        $this->createIndex('report_index_longitude', \app\models\db\Report::tableName(), 'longitude');
        $this->createIndex('report_index_sent_email_count', \app\models\db\Report::tableName(), 'sent_email_count');
        $this->createIndex('report_index_highlighted', \app\models\db\Report::tableName(), 'highlighted');
        $this->createIndex('email_index_to', \app\models\db\Email::tableName(), 'to');
        $this->createIndex('city_index_email_address', \app\models\db\City::tableName(), 'email_address');
    }

    public function down()
    {
        $this->dropIndex('report_activity_index_is_active_task', \app\models\db\ReportActivity::tableName());
        $this->dropIndex('report_index_name', \app\models\db\Report::tableName());
        $this->dropIndex('report_index_latitude', \app\models\db\Report::tableName());
        $this->dropIndex('report_index_longitude', \app\models\db\Report::tableName());
        $this->dropIndex('report_index_sent_email_count', \app\models\db\Report::tableName());
        $this->dropIndex('report_index_highlighted', \app\models\db\Report::tableName());
        $this->dropIndex('email_index_to', \app\models\db\Email::tableName());
        $this->dropIndex('city_index_email_address', \app\models\db\City::tableName());
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
