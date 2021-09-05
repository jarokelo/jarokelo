<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Class m210720_181356_create_table_report_answers
 */
class m210720_181356_create_table_report_answers extends Migration
{
    const TABLE_NAME = 'report_answers';
    const INDEX_REPORT_ID = 'index_report_answers_report_id';
    const INDEX_REPORT_CATEGORY_ID = 'index_report_answers_report_category_id';
    const INDEX_REPORT_TAXONOMY_ID = 'index_report_answers_report_taxonomy_id';
    const INDEX_CUSTOM_FORM_ID = 'index_report_answers_custom_form_id';
    const FK_REPORT_ID = 'fk_report_answers_report_id_report_id';
    const FK_REPORT_CATEGORY_ID = 'fk_report_answers_report_category_id_report_category_id';
    const FK_REPORT_TAXONOMY_ID = 'fk_report_answers_report_taxonomy_id_report_taxonomy_id';
    const FK_CUSTOM_FORM_ID = 'fk_report_answers_custom_form_id_custom_forms_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->primaryKey()->notNull()->unsigned(),
                'report_id' => $this->bigInteger()->notNull(),
                'report_category_id' => $this->integer()->notNull(),
                'report_taxonomy_id' => $this->integer()->unsigned()->null(),
                'custom_form_id' => $this->integer()->unsigned()->notNull(),
                'answers' => $this->text()->notNull(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->createIndex(self::INDEX_REPORT_ID, self::TABLE_NAME, 'report_id', true);
        $this->createIndex(self::INDEX_REPORT_CATEGORY_ID, self::TABLE_NAME, 'report_category_id');
        $this->createIndex(self::INDEX_REPORT_TAXONOMY_ID, self::TABLE_NAME, 'report_taxonomy_id');
        $this->createIndex(self::INDEX_CUSTOM_FORM_ID, self::TABLE_NAME, 'custom_form_id');
        $this->addForeignKey(self::FK_REPORT_ID, self::TABLE_NAME, 'report_id', \app\models\db\Report::tableName(), 'id');
        $this->addForeignKey(self::FK_REPORT_CATEGORY_ID, self::TABLE_NAME, 'report_category_id', \app\models\db\ReportCategory::tableName(), 'id');
        $this->addForeignKey(self::FK_REPORT_TAXONOMY_ID, self::TABLE_NAME, 'report_taxonomy_id', \app\models\db\ReportTaxonomy::tableName(), 'id');
        $this->addForeignKey(self::FK_CUSTOM_FORM_ID, self::TABLE_NAME, 'custom_form_id', \app\models\db\CustomForm::tableName(), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(self::FK_REPORT_ID, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_REPORT_CATEGORY_ID, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_REPORT_TAXONOMY_ID, self::TABLE_NAME);
        $this->dropForeignKey(self::FK_CUSTOM_FORM_ID, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_CUSTOM_FORM_ID, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_REPORT_TAXONOMY_ID, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_REPORT_CATEGORY_ID, self::TABLE_NAME);
        $this->dropIndex(self::INDEX_REPORT_ID, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
