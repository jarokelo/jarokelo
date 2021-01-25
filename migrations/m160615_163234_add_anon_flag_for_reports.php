<?php

use yii\db\Migration;

class m160615_163234_add_anon_flag_for_reports extends Migration
{
    public function up()
    {
        $this->addColumn('report', 'anonymous', $this->smallInteger()->notNull()->defaultValue(0)->unsigned());
    }

    public function down()
    {
        $this->dropColumn('report', 'anonymous');
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
