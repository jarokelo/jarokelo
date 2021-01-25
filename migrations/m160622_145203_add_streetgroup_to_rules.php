<?php

use yii\db\Migration;

class m160622_145203_add_streetgroup_to_rules extends Migration
{
    public function up()
    {
        $this->addColumn('rule', 'street_group_id', $this->bigInteger());
        $this->createIndex('rule__street_group_id', 'rule', 'street_group_id');
        $this->addForeignKey('rule__street_group_id', 'rule', 'street_group_id', 'street_group', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('rule__street_group_id', 'rule');
        $this->dropIndex('rule__street_group_id', 'rule');
        $this->dropColumn('rule', 'street_group_id');
    }
}
