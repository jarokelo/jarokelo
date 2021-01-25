<?php

use yii\db\Migration;

class m160707_102246_sluggable_city_and_district extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'slug', \yii\db\Schema::TYPE_STRING . ' AFTER name');
        $this->addColumn('district', 'slug', \yii\db\Schema::TYPE_STRING . ' AFTER name');

        $this->createIndex('city_index_slug', 'city', 'slug', true);
        $this->createIndex('district_index_slug', 'district', 'slug', true);

        $cities = \app\models\db\City::find()->all();
        foreach ($cities as $city) {
            $city->save();
        }

        $districts = \app\models\db\District::find()->all();
        foreach ($districts as $district) {
            $district->save();
        }
    }

    public function down()
    {
        $this->dropIndex('district_index_slug', 'district');
        $this->dropIndex('city_index_slug', 'city');

        $this->dropColumn('district', 'slug');
        $this->dropColumn('city', 'slug');
    }
}
