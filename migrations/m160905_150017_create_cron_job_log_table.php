<?php

use yii\db\Migration;

/**
 * Handles the creation for table `cron_job_log`.
 */
class m160905_150017_create_cron_job_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cron_job_log', [
            'id' => $this->bigPrimaryKey(),
            'type' => $this->smallInteger(),
            'output' => $this->text(),
            'error_message' => $this->text(),
            'runtime' => $this->float(),
            'created_at' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cron_job_log');
    }
}
