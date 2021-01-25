<?php

use yii\db\Migration;

class m170911_185556_add_answer_classification extends Migration
{
    public function up()
    {
        $this->createTable('report_answer_classification', [
            'id' => $this->bigPrimaryKey(),
            'report_activity_id' => $this->bigInteger(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
            'score' => $this->smallInteger(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('report_answer_classification_index_report_activity', 'report_answer_classification', 'report_activity_id');

        $this->addForeignKey('report_answer_classification_FK_report_activity', 'report_answer_classification', 'report_activity_id', 'report_activity', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('report_answer_classification_FK_report_activity', 'report_answer_classification');

        $this->dropIndex('report_answer_classification_index_report_activity', 'report_answer_classification');

        $this->dropTable('report_answer_classification');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
