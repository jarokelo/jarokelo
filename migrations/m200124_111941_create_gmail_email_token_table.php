<?php

use yii\db\Migration;
use app\components\helpers\Migration as MigrationHelper;

/**
 * Handles the creation of table `gmail_email_token`.
 */
class m200124_111941_create_gmail_email_token_table extends Migration
{
    const TABLE_GMAIL_EMAIL_TOKEN = 'gmail_email_token';

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->createTable(
            self::TABLE_GMAIL_EMAIL_TOKEN,
            [
                'email' => $this->string(128),
                'created_at' => $this->bigInteger()->notNull()->unsigned(),
                'updated_at' => $this->bigInteger()->notNull()->unsigned(),
                'token' => $this->text(),
            ],
            MigrationHelper::TABLE_OPTIONS
        );
        $this->addPrimaryKey(
            'email_pk',
            self::TABLE_GMAIL_EMAIL_TOKEN,
            ['email']
        );
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->dropTable(self::TABLE_GMAIL_EMAIL_TOKEN);
    }
}
