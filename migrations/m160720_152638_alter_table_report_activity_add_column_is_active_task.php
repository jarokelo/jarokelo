<?php

use yii\db\Migration;

class m160720_152638_alter_table_report_activity_add_column_is_active_task extends Migration
{
    public function up()
    {
        $this->addColumn('report_activity', 'is_active_task', $this->boolean()->notNull()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('report_activity', 'is_active_task');
    }
}
