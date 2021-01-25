<?php

use yii\db\Migration;

class m160725_100951_add_notification_options extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'notification', $this->boolean()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('user', 'notification');
    }
}
