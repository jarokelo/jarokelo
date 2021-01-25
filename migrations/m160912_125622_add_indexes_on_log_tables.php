<?php

use yii\db\Migration;

class m160912_125622_add_indexes_on_log_tables extends Migration
{
    public function up()
    {
        $this->createIndex('cron_job_log_index_created_at', \app\models\db\CronLog::tableName(), 'created_at');
        $this->createIndex('cron_job_log_index_runtime', \app\models\db\CronLog::tableName(), 'runtime');
        $this->createIndex('cron_job_log_index_type', \app\models\db\CronLog::tableName(), 'type');
    }

    public function down()
    {
        $this->dropIndex('cron_job_log_index_created_at', \app\models\db\CronLog::tableName());
        $this->dropIndex('cron_job_log_index_runtime', \app\models\db\CronLog::tableName());
        $this->dropIndex('cron_job_log_index_type', \app\models\db\CronLog::tableName());
    }
}
