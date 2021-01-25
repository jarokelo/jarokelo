<?php

use yii\db\Migration;

class m160718_100851_add_column_pwd_recovery_token extends Migration
{
    const TBL_USER = 'user';

    public function up()
    {
        $this->addColumn(self::TBL_USER, 'password_recovery_token', $this->string()->after('password_hash'));
    }

    public function down()
    {
        $this->dropColumn(self::TBL_USER, 'password_recovery_token');
    }
}
