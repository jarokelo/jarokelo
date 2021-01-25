<?php

use yii\db\Migration;

class m160913_151603_add_district_article extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\db\District::tableName(), 'article', $this->string()->after('name')->defaultValue('a'));
    }

    public function down()
    {
        $this->dropColumn(\app\models\db\District::tableName(), 'article');
    }
}
