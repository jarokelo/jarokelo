<?php

use yii\db\Migration;

class m160630_100248_add_profile_additional_info extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user', 'city_id', $this->bigInteger());
        $this->addColumn('user', 'district_id', $this->bigInteger());

        $this->createIndex('user__city_id_idx', 'user', 'city_id');
        $this->createIndex('user__district_id_idx', 'user', 'district_id');

        $this->addForeignKey('user__city_id_fk', 'user', 'city_id', 'city', 'id');
        $this->addForeignKey('user__district_id_fk', 'user', 'district_id', 'district', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('user__city_id_fk', 'user');
        $this->dropForeignKey('user__district_id_fk', 'user');

        $this->dropIndex('user__city_id_idx', 'user');
        $this->dropIndex('user__district_id_idx', 'user');

        $this->dropColumn('user', 'city_id');
        $this->dropColumn('user', 'district_id');
    }
}
