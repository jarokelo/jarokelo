<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 *
 */
class m200310_154244_config extends Migration
{
    const TABLE_CONFIG = 'config';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_CONFIG,
            [
                'id' => $this->bigPrimaryKey(),
                'key' => $this->string(64),
                'category' => $this->string(64),
                'value' => $this->text(),
                'created_at' => $this->bigInteger()->notNull()->unsigned(),
                'updated_at' => $this->bigInteger()->notNull()->unsigned(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::TABLE_CONFIG);
    }
}
