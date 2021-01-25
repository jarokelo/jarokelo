<?php

use yii\db\Migration;

class m160725_124414_add_lat_long_to_districts extends Migration
{
    public function up()
    {
        $this->addColumn('district', 'latitude', $this->decimal(10, 8)->notNull());
        $this->addColumn('district', 'longitude', $this->decimal(11, 8)->notNull());
    }

    public function down()
    {
        $this->dropColumn('district', 'latitude');
        $this->dropColumn('district', 'longitude');
    }
}
