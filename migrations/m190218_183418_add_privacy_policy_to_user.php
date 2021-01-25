<?php

use yii\db\Migration;
use app\models\db\User;

class m190218_183418_add_privacy_policy_to_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(User::tableName(), 'privacy_policy', $this->smallInteger()->unsigned());

        // adding initial value retroactively
        User::updateAll(['privacy_policy' => User::PRIVACY_POLICY_REJECTED]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn(User::tableName(), 'privacy_policy');
    }
}
