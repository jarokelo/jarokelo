<?php

use yii\db\Migration;

class m160728_120922_add_city_id_to_streetgroups extends Migration
{
    public function up()
    {
        $this->addColumn('street_group', 'city_id', $this->bigInteger(20)->notNull());
        $this->createIndex('street_group_index_city', 'street_group', 'city_id');

        $streetGroups = \app\models\db\StreetGroup::find()->all();
        /** @var \app\models\db\StreetGroup $streetGroup */
        foreach ($streetGroups as $streetGroup) {
            $streets = $streetGroup->getStreets()->all();
            /** @var \app\models\db\Street $street */
            foreach ($streets as $street) {
                $cityId = $street->city_id;
                $streetGroup->city_id = $cityId;
                break;
            }
            $streetGroup->save(true, ['city_id']);
        }

        $this->addForeignKey('street_group__city_id_fk', 'street_group', 'city_id', 'city', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('street_group__city_id_fk', 'street_group');
        $this->dropIndex('street_group_index_city', 'street_group');
        $this->dropColumn('street_group', 'city_id');
    }
}
