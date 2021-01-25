<?php

use yii\db\Migration;

class m160316_111833_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->bigPrimaryKey(),
            'email' => $this->string()->unique()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'first_name' => $this->string()->notNull(),
            'phone_number' => $this->string(),
            'image_file_name' => $this->string(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'last_login_at' => $this->bigInteger()->unsigned(),
            'last_login_ip' => $this->string(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('user_auth', [
            'user_id' => $this->bigInteger()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),

            'PRIMARY KEY(`user_id`, `source`)',
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('user_index_email_status', 'user', ['email', 'status']);
        $this->createIndex('user_auth_index_source', 'user_auth', ['source', 'source_id']);

        $this->addForeignKey('user_auth_FK_user', 'user_auth', 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_auth_FK_user', 'user_auth');

        $this->dropIndex('user_index_email_status', 'user');
        $this->dropIndex('user_auth_index_source', 'user_auth');

        $this->dropTable('user');
        $this->dropTable('user_auth');
    }
}
