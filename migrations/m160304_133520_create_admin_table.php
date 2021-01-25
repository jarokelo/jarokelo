<?php

use yii\db\Migration;

class m160304_133520_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('admin', [
            'id' => $this->bigPrimaryKey(),
            'email' => $this->string()->unique()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'first_name' => $this->string()->notNull(),
            'phone_number' => $this->string(),
            'image_file_name' => $this->string(),
            'permissions' => $this->bigInteger()->notNull()->unsigned()->defaultValue(0),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'last_login_at' => $this->bigInteger()->unsigned(),
            'last_login_ip' => $this->string(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createIndex('admin_index_email_status', 'admin', ['email', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('admin_index_email_status', 'admin');

        $this->dropTable('admin');
    }
}
