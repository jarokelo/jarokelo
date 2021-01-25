<?php

use yii\db\Migration;

class m160913_080556_city_and_district_filter_names extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\db\City::tableName(), 'name_filter', $this->string()->after('name'));
        $this->addColumn(\app\models\db\District::tableName(), 'name_filter', $this->string()->after('name'));
    }

    public function down()
    {
        $this->dropColumn(\app\models\db\City::tableName(), 'name_filter');
        $this->dropColumn(\app\models\db\District::tableName(), 'name_filter');
    }
}
