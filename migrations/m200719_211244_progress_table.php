<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

class m200719_211244_progress_table extends Migration
{
    const TABLE_NAME = 'progress';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->bigPrimaryKey(),
                'amount' => $this->bigInteger(),
                'created_by' => $this->bigInteger()->notNull(),
                'updated_by' => $this->bigInteger()->notNull(),
                'created_at' => $this->bigInteger()->notNull()->unsigned(),
                'updated_at' => $this->bigInteger()->notNull()->unsigned(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );

        $this->addForeignKey('progress_created_by_FK_admin', self::TABLE_NAME, 'created_by', 'admin', 'id');
        $this->addForeignKey('progress_updated_by_FK_admin', self::TABLE_NAME, 'updated_by', 'admin', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('progress_created_by_FK_admin', self::TABLE_NAME);
        $this->dropForeignKey('progress_updated_by_FK_admin', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
