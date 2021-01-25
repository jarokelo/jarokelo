<?php

use yii\db\Migration;

class m160726_092605_add_lat_long_for_city extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'latitude', $this->decimal(10, 8)->notNull());
        $this->addColumn('city', 'longitude', $this->decimal(11, 8)->notNull());
    }

    public function down()
    {
        $this->dropColumn('city', 'latitude');
        $this->dropColumn('city', 'longitude');
    }
}
