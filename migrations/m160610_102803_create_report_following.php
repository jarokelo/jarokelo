<?php

use yii\db\Migration;

/**
 * Handles the creation for table `report_following`.
 */
class m160610_102803_create_report_following extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('report_following', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'report_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('report_following__user_id', 'report_following', 'user_id');
        $this->createIndex('report_following__report_id', 'report_following', 'report_id');

        $this->addForeignKey('report_following__user_id', 'report_following', 'user_id', 'user', 'id');
        $this->addForeignKey('report_following__report_id', 'report_following', 'report_id', 'report', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('report_following__user_id', 'report_following');
        $this->dropForeignKey('report_following__report_id', 'report_following');

        $this->dropIndex('report_following__user_id', 'report_following');
        $this->dropIndex('report_following__report_id', 'report_following');

        $this->dropTable('report_following');
    }
}
