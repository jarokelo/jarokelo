<?php

use yii\db\Migration;
use app\models\db\MapLayer;

class m201116_190151_alter_table_map_layers_add_name extends Migration
{
    public function up()
    {
        $this->addColumn(
            MapLayer::tableName(),
            'name',
            $this->string(64)->after('id')
        );
    }

    public function down()
    {
        $this->dropColumn(
            MapLayer::tableName(),
            'name'
        );
    }
}
