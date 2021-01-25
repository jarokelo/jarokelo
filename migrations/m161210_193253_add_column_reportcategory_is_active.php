<?php

use yii\db\Migration;

class m161210_193253_add_column_reportcategory_is_active extends Migration
{
    public function up()
    {
        $this->addColumn('report_category', 'is_active', $this->boolean()->defaultValue(1));

        $this->execute('UPDATE report_category SET is_active=:is_active WHERE id=:nearby_school_id', [
            ':is_active' => 0,
            ':nearby_school_id' => 23,
        ]);
    }

    public function down()
    {
        $this->dropColumn('report_category', 'is_active');
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
