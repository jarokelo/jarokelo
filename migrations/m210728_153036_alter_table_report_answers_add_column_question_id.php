<?php

use yii\db\Migration;
use app\models\db\ReportAnswer;
use app\models\db\CustomQuestion;

/**
 * Class m210728_153036_alter_table_report_answers_add_column_question_id
 */
class m210728_153036_alter_table_report_answers_add_column_question_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(ReportAnswer::tableName(), 'custom_question_id', $this->integer()->notNull()->unsigned());
        $this->createIndex($name = ReportAnswer::tableName() . 'custom_question_id', ReportAnswer::tableName(), 'custom_question_id');
        $this->addForeignKey('fk_' . $name, ReportAnswer::tableName(), 'custom_question_id', CustomQuestion::tableName(), 'id');
        $this->dropForeignKey('fk_report_answers_report_id_report_id', ReportAnswer::tableName());
        $this->dropIndex('index_report_answers_report_id', ReportAnswer::tableName());
        $this->createIndex('index_report_answers_report_id', ReportAnswer::tableName(), 'report_id');
        $this->addForeignKey('fk_report_answers_report_id_report_id', ReportAnswer::tableName(), 'report_id', \app\models\db\Report::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_' . ($name = ReportAnswer::tableName() . 'custom_question_id'), ReportAnswer::tableName());
        $this->dropIndex($name, ReportAnswer::tableName());
        $this->dropColumn(ReportAnswer::tableName(), 'custom_question_id');
    }
}
