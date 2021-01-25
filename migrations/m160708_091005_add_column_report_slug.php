<?php

use yii\db\Migration;

class m160708_091005_add_column_report_slug extends Migration
{
    public function up()
    {
        $this->addColumn('report', 'slug', \yii\db\Schema::TYPE_STRING . ' AFTER name');
        $this->createIndex('report_index_slug', 'report', 'slug');

        $reports = \app\models\db\Report::find()->all();
        foreach ($reports as $report) {
            $report->save(true, ['slug']);
        }
    }

    public function down()
    {
        $this->dropIndex('report_index_slug', 'report');
        $this->dropColumn('report', 'slug');
    }
}
