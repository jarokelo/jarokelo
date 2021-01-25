<?php

use yii\db\Migration;

class m201116_180249_map_layer_report_connector extends Migration
{
    const TABLE = 'report_map_layer';
    const INDEX = 'report_map_layer_unique_report_id_map_layer_id';
    const FK_REPORT_ID = 'fk_report_map_layer_report_id';
    const FK_MAP_LAYER_ID = 'fk_report_map_layer_map_layer_id';

    public function up()
    {
        $this->createTable(
            self::TABLE,
            [
                'report_id' => $this->bigInteger(), // Copied from report migration
                'map_layer_id' => $this->integer()->unsigned(), // Copied from map layer migration
            ]
        );
        $this->createIndex(
            self::INDEX,
            self::TABLE,
            ['report_id', 'map_layer_id'],
            true
        );
        $this->addForeignKey(
            self::FK_REPORT_ID,
            self::TABLE,
            'report_id',
            'report',
            'id'
        );
        $this->addForeignKey(
            self::FK_MAP_LAYER_ID,
            self::TABLE,
            'map_layer_id',
            'map_layer',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey(self::FK_REPORT_ID, self::TABLE);
        $this->dropForeignKey(self::FK_MAP_LAYER_ID, self::TABLE);
        $this->dropIndex(self::INDEX, self::TABLE);
        $this->dropTable(self::TABLE);
    }
}
