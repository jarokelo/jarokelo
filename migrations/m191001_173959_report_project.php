<?php

use yii\db\Migration;
use app\models\db\Report;

class m191001_173959_report_project extends Migration
{
    public function up()
    {
        $this->addColumn(
            Report::tableName(),
            'project',
            $this->smallInteger()->notNull()->unsigned()
        );

        try {
            // Checking if column exist at all
            if ($this->getDb()->getSchema()->getTableSchema(Report::tableName())->getColumn('inclusion')) {
                // Removing previously created column..
                $this->dropColumn(Report::tableName(), 'inclusion');
            }
        } catch (\Exception $e) {
            // Something failed, upon fetching schema, do nothing..
        }
    }

    public function down()
    {
        $this->dropColumn(Report::tableName(), 'project');
    }
}
