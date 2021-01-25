<?php

use yii\db\Migration;

class m160712_144831_alter_table_report_make_user_id_nullable extends Migration
{
    public function up()
    {
        $this->alterColumn('report', 'user_id', $this->bigInteger());
    }

    public function down()
    {
        $this->dropForeignKey('report_FK_user', 'report');
        $this->alterColumn('report', 'user_id', $this->bigInteger()->notNull());
        $this->addForeignKey('report_FK_user', 'report', 'user_id', 'user', 'id');
    }
}
