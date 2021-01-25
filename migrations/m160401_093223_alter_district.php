<?php

use yii\db\Migration;

class m160401_093223_alter_district extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('district', 'number', $this->smallInteger()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('district', 'number');
    }
}
