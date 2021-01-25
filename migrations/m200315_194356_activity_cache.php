<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 *
 */
class m200315_194356_activity_cache extends Migration
{
    const TABLE_NAME = 'activity_cache';

    /**
     * @var string
     */
    protected $foreignKeyActivity;

    /**
     * @var string
     */
    protected $foreignKeyAdmin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->foreignKeyActivity = self::TABLE_NAME . '_FK_report_activity';
        $this->foreignKeyAdmin = self::TABLE_NAME . '_FK_admin';
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_NAME,
            [
                'id' => $this->bigPrimaryKey(),
                'report_activity_id' => $this->bigInteger(),
                'admin_id' => $this->bigInteger(),
                'created_at' => $this->bigInteger()->notNull()->unsigned(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );

        $this->addForeignKey(
            $this->foreignKeyActivity,
            self::TABLE_NAME,
            'report_activity_id',
            'report_activity',
            'id'
        );

        $this->addForeignKey(
            $this->foreignKeyAdmin,
            self::TABLE_NAME,
            'admin_id',
            'admin',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey($this->foreignKeyActivity, self::TABLE_NAME);
        $this->dropForeignKey($this->foreignKeyAdmin, self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
