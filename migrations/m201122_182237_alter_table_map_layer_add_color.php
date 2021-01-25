<?php

use yii\db\Migration;
use app\models\db\MapLayer;

class m201122_182237_alter_table_map_layer_add_color extends Migration
{
    public function up()
    {
        $this->addColumn(
            MapLayer::tableName(),
            'color',
            $this->string(32)->after('name')
        );
    }

    public function down()
    {
        $this->dropColumn(
            MapLayer::tableName(),
            'color'
        );
    }
}
