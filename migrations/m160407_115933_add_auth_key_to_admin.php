<?php

use yii\db\Migration;

class m160407_115933_add_auth_key_to_admin extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('admin', 'auth_key', $this->string(32));
        $this->addColumn('admin', 'auth_key_expiration', $this->bigInteger());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('admin', 'auth_key');
        $this->dropColumn('admin', 'auth_key_expiration');
    }
}
