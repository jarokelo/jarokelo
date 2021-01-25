<?php

use yii\db\Migration;

class m160419_095618_add_columns_to_report extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('report', 'sent_email_count', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('report', 'post_code', $this->string()->notNull());
        $this->addColumn('report', 'street_name', $this->string()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('report', 'sent_email_count');
        $this->dropColumn('report', 'post_code');
        $this->dropColumn('report', 'street_name');
    }
}
