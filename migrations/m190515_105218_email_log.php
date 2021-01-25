<?php

use yii\db\Migration;
use app\models\db\EmailLog;

class m190515_105218_email_log extends Migration
{
    public function up()
    {
        $this->createTable(EmailLog::tableName(), [
            'id' => $this->bigPrimaryKey(),
            '_get' => $this->text(), // type json is not supported current versions
            '_post' => $this->text(),
            '_server' => $this->text(),
            'from' => $this->string(),
            'to' => $this->string(),
            'subject' => $this->string(),
            'is_successful' => $this->boolean()->unsigned(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);
    }

    public function down()
    {
        $this->dropTable(EmailLog::tableName());
    }
}
