<?php

use yii\db\Migration;

class m160729_081823_create_mail_log extends Migration
{
    const TABLE_MAIL_LOG = 'mail_log';

    public function up()
    {
        $this->createTable(self::TABLE_MAIL_LOG, [
            'id' => $this->bigPrimaryKey(),
            'type' => $this->integer()->notNull(),
            'type_info' => $this->string(),
            'user_id' => $this->bigInteger(),
            'sent_at' => $this->bigInteger(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);
    }

    public function down()
    {
        $this->dropTable(self::TABLE_MAIL_LOG);
    }
}
