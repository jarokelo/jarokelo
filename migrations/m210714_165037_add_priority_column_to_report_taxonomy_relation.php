<?php

use yii\db\Migration;
use app\models\db\ReportTaxonomyRelation;

/**
 * Class m210714_165037_add_priority_column_to_report_taxonomy_relation
 */
class m210714_165037_add_priority_column_to_report_taxonomy_relation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(
            ReportTaxonomyRelation::tableName(),
            'priority',
            $this->integer()->notNull()->unsigned()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn(
            ReportTaxonomyRelation::tableName(),
            'priority'
        );
    }
}
