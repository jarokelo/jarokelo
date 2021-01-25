<?php

use yii\db\Migration;
use app\models\db\User;

class m181229_181105_update_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'activated_at', $this->bigInteger()->null()->unsigned());

        // loading initial value to users
        foreach (User::find()->all() as $user) {
            $user->updateAttributes(
                [
                    'activated_at' => $user['created_at'],
                ]
            );
        }
    }

    public function down()
    {
        $this->dropColumn('user', 'activated_at');
    }
}
