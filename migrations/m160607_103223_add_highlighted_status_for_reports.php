<?php

use yii\db\Migration;

class m160607_103223_add_highlighted_status_for_reports extends Migration
{
    public function up()
    {
        $this->addColumn('report', 'highlighted', $this->smallInteger()->notNull()->defaultValue(0)->unsigned());
    }

    public function down()
    {
        $this->dropColumn('report', 'highlighted');
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
