<?php

use yii\db\Migration;
use app\models\db\ReportCategory;
use app\models\db\ReportTaxonomy;

/**
 * Class m210702_181257_report_category_and_report_taxonomy_add_unique_index_on_name
 */
class m210702_181257_report_category_and_report_taxonomy_add_unique_index_on_name extends Migration
{
    const UNIQUE_INDEX_REPORT_CATEGORY = 'report_category_name_unique';
    const UNIQUE_INDEX_REPORT_TAXONOMY = 'report_taxonomy_name_unique';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // Removing duplication
        try {
            $model = ReportCategory::find()
                ->where(['id' => 3])
                ->one();

            if ($model) {
                $model->delete();
            }
        } catch (\Exception $e) {
            // ..
        }

        $this->createIndex(self::UNIQUE_INDEX_REPORT_CATEGORY, ReportCategory::tableName(), 'name', true);
        $this->createIndex(self::UNIQUE_INDEX_REPORT_TAXONOMY, ReportTaxonomy::tableName(), 'name', true);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex(self::UNIQUE_INDEX_REPORT_CATEGORY, ReportCategory::tableName());
        $this->dropIndex(self::UNIQUE_INDEX_REPORT_TAXONOMY, ReportTaxonomy::tableName());
    }
}
