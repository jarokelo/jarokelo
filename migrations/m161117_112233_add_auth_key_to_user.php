<?php

use yii\db\Migration;

class m161117_112233_add_auth_key_to_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user', 'auth_key', $this->string(32)->after('password_recovery_token'));
        $this->addColumn('user', 'auth_key_expiration', $this->bigInteger()->after('auth_key'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'auth_key');
        $this->dropColumn('user', 'auth_key_expiration');
    }
}
