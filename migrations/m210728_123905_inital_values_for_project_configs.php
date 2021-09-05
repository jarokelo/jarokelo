<?php

use yii\db\Migration;
use app\models\db\ProjectConfig;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Class m210728_123905_inital_values_for_project_configs
 */
class m210728_123905_inital_values_for_project_configs extends Migration
{
    const TABLE_NAME = 'project_config';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Recreating this table to fix the indexes and add initial values
        //$this->dropTable(ProjectConfig::tableName());

        $this->createTable(
            self::TABLE_NAME,
            [
                'key' => $this->string(255)->notNull(),
                'value' => $this->boolean()->notNull()->defaultValue(1),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->createIndex(self::TABLE_NAME . '__key', self::TABLE_NAME, ['key'], true);

        foreach (ProjectConfig::getFilterKeys() as $name => $_) {
            try {
                $projectConfig = (new ProjectConfig(
                    [
                        'key' => $name,
                        'value' => ProjectConfig::STATUS_ACTIVE,
                    ]
                ));
                $projectConfig->save();
            } catch (\Exception $e) {
                // ..
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
