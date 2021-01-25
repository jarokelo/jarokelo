<?php

use yii\db\Migration;

class m160728_100618_add_notification_types_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'notification_owned', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('user', 'notification_followed', $this->smallInteger()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('user', 'notification_owned');
        $this->dropColumn('user', 'notification_followed');
    }
}
