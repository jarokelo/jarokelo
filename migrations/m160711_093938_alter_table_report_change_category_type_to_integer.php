<?php

use yii\db\Migration;

class m160711_093938_alter_table_report_change_category_type_to_integer extends Migration
{
    const CATEGORIES = [
        'abandoned_vehicle' => 'Elhagyott jármű',
        'accessibility' => 'Akadálymentesség',
        'bicycle' => 'Kerékpár',
        'construction' => 'Építkezés',
        'drainage' => 'Csatornázás',
        'graffiti' => 'Graffiti',
        'illegal_construction' => 'Illegális építkezés',
        'monument' => 'Emlékmű',
        'nature' => 'Környezet, természet',
        'other' => 'Egyéb',
        'parking' => 'Parkoló',
        'parks' => 'Parkok és zöld területek',
        'potholes' => 'Kátyúk',
        'public_lighting' => 'Közvilágítás',
        'public_order' => 'Közrend',
        'public_transport' => 'Tömegközlekedés',
        'public_utilities' => 'Közművek',
        'sanitation' => 'Köztisztaság',
        'schools_neighbourhood' => 'Iskolák, óvodák és bölcsődék környéke',
        'sidewalk' => 'Járda',
        'street_signs' => 'Utcanévtábla',
        'traffic_engineering' => 'Forgalomtechnika',

    ];

    public function up()
    {
        $this->createTable('report_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        foreach (self::CATEGORIES as $cat_abbr => $cat_name) {
            $this->insert('report_category', ['name' => $cat_name]);
            $lastId = Yii::$app->db->lastInsertID;
            $this->update('report', ['category' => $lastId], ['category' => $cat_abbr]);
            $this->update('report_original', ['category' => $lastId], ['category' => $cat_abbr]);
            $this->update('rule', ['category' => $lastId], ['category' => $cat_abbr]);
            $this->update('report_activity', ['original_value' => $lastId], ['original_value' => $cat_abbr]);
            $this->update('report_activity', ['new_value' => $lastId], ['new_value' => $cat_abbr]);
        }

        $this->renameColumn('report', 'category', 'report_category_id');
        $this->alterColumn('report', 'report_category_id', $this->integer());
        $this->addForeignKey('report_FK_report_category', 'report', 'report_category_id', 'report_category', 'id');

        $this->renameColumn('report_original', 'category', 'report_category_id');
        $this->alterColumn('report_original', 'report_category_id', $this->integer()->notNull());
        $this->addForeignKey('report_original_FK_report_category', 'report_original', 'report_category_id', 'report_category', 'id');

        $this->renameColumn('rule', 'category', 'report_category_id');
        $this->update('rule', ['report_category_id' => null], ['report_category_id' => '']);
        $this->alterColumn('rule', 'report_category_id', $this->integer());
        $this->addForeignKey('rule_FK_report_category', 'rule', 'report_category_id', 'report_category', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('report_FK_report_category', 'report');
        $this->renameColumn('report', 'report_category_id', 'category');
        $this->alterColumn('report', 'category', $this->string());

        $this->dropForeignKey('report_original_FK_report_category', 'report_original');
        $this->renameColumn('report_original', 'report_category_id', 'category');
        $this->alterColumn('report_original', 'category', $this->string()->notNull());

        $this->dropForeignKey('rule_FK_report_category', 'rule');
        $this->renameColumn('rule', 'report_category_id', 'category');
        $this->alterColumn('rule', 'category', $this->string());

        foreach ((new \yii\db\Query())->select(['id', 'name'])->from('report_category')->all() as $category) {
            $oldValue = array_search($category['name'], self::CATEGORIES);
            $this->update('report', ['category' => $oldValue], ['category' => $category['id']]);
            $this->update('report_original', ['category' => $oldValue], ['category' => $category['id']]);
            $this->update('rule', ['category' => $oldValue], ['category' => $category['id']]);
            $this->update('report_activity', ['original_value' => $oldValue], ['original_value' => $category['id']]);
            $this->update('report_activity', ['new_value' => $oldValue], ['new_value' => $category['id']]);
        }

        $this->dropTable('report_category');
    }
}
