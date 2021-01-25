<?php

use yii\db\Migration;

class m160823_093628_add_indexes_for_timestamps extends Migration
{
    public function up()
    {
        $this->createIndex('report_index_created_at', \app\models\db\Report::tableName(), 'created_at');
    }

    public function down()
    {
        $this->dropIndex('report_index_created_at', \app\models\db\Report::tableName());
    }
}
