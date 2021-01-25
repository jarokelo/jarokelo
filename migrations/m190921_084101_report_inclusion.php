<?php

use yii\db\Migration;
use app\models\db\Report;

class m190921_084101_report_inclusion extends Migration
{
    public function up()
    {
        $this->addColumn(
            Report::tableName(),
            'inclusion',
            $this->boolean()->notNull()->unsigned()->defaultValue(false)
        );
    }

    public function down()
    {
        $this->dropColumn(Report::tableName(), 'inclusion');
    }
}
