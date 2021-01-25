<?php

use yii\db\Migration;

class m160802_133625_add_rate_limit_field_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'api_rate_limit', $this->bigInteger()->notNull()->defaultValue(1));
        $this->addColumn('user', 'api_allowance', $this->bigInteger());
        $this->addColumn('user', 'api_allowance_updated_at', $this->bigInteger());
    }

    public function down()
    {
        $this->dropColumn('user', 'api_allowance_updated_at');
        $this->dropColumn('user', 'api_allowance');
        $this->dropColumn('user', 'api_rate_limit');
    }
}
