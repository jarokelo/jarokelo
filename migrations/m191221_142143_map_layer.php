<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 *
 */
class m191221_142143_map_layer extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            'map_layer',
            [
                'id' => $this->primaryKey()->unsigned(),
                'data' => 'MEDIUMBLOB',
                'lat' => $this->double()->notNull(),
                'lng' => $this->double()->notNull(),
                'zoom' => $this->double()->notNull(),
                'created_by' => $this->bigInteger()->notNull(),
                'updated_by' => $this->bigInteger()->notNull(),
                'created_at' => $this->bigInteger()->notNull()->unsigned(),
                'updated_at' => $this->bigInteger()->notNull()->unsigned(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );

        $this->createIndex('map_layer_created_by', 'map_layer', 'created_by');
        $this->createIndex('map_layer_updated_by', 'map_layer', 'updated_by');
        $this->addForeignKey('map_layer_created_by_FK_admin', 'map_layer', 'created_by', 'admin', 'id');
        $this->addForeignKey('map_layer_updated_by_FK_admin', 'map_layer', 'updated_by', 'admin', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('map_layer_created_by_FK_admin', 'map_layer');
        $this->dropForeignKey('map_layer_updated_by_FK_admin', 'map_layer');
        $this->dropTable('map_layer');
    }
}
