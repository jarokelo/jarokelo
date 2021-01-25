<?php

use yii\db\Migration;

class m160722_102635_add_slug_to_institution extends Migration
{
    public function up()
    {
        $this->addColumn('institution', 'slug', \yii\db\Schema::TYPE_STRING . ' AFTER name');
        $this->createIndex('institution_index_slug', 'institution', 'slug');

        $institutions = \app\models\db\Institution::find()->all();
        foreach ($institutions as $institution) {
            $institution->save(true, ['slug']);
        }
    }

    public function down()
    {
        $this->dropIndex('institution_index_slug', 'institution');
        $this->dropColumn('institution', 'slug');
    }
}
