<?php

use yii\db\Migration;

class m160413_112811_city_name_unique extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex('city_index_name', 'city', 'name', true);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('city_index_name', 'city');
    }
}
