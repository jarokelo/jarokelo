<?php

use yii\db\Migration;

class m160627_125610_report_activity_ratings extends Migration
{
    public function up()
    {
        $this->createTable('report_activity_ratings', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'activity_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
            'state' => $this->smallInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('report_activity__user_id', 'report_activity_ratings', 'user_id');
        $this->createIndex('report_activity__activity_id', 'report_activity_ratings', 'activity_id');

        $this->addForeignKey('report_activity__user_id', 'report_activity_ratings', 'user_id', 'user', 'id');
        $this->addForeignKey('report_activity__activity_id', 'report_activity_ratings', 'activity_id', 'report_activity', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('report_activity__user_id', 'report_activity_ratings');
        $this->dropForeignKey('report_activity__activity_id', 'report_activity_ratings');

        $this->dropIndex('report_activity__user_id', 'report_activity_ratings');
        $this->dropIndex('report_activity__activity_id', 'report_activity_ratings');

        $this->dropTable('report_activity_ratings');
    }
}
