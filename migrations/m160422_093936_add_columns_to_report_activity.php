<?php

use yii\db\Migration;

class m160422_093936_add_columns_to_report_activity extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('report_activity', 'is_latest', $this->boolean()->notNull()->defaultValue(0));
        $this->addColumn('report_activity', 'is_hidden', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('report_activity', 'is_latest');
        $this->dropColumn('report_activity', 'is_hidden');
    }
}
