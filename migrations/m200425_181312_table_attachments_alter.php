<?php

use yii\db\Migration;
use app\models\db\ReportAttachment;
use app\models\db\ReportAttachmentOriginal;

class m200425_181312_table_attachments_alter extends Migration
{
    public function up()
    {
        $this->addColumn(ReportAttachment::tableName(), 'storage', $this->smallInteger()->defaultValue(0));
        $this->addColumn(ReportAttachmentOriginal::tableName(), 'storage', $this->smallinteger()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn(ReportAttachment::tableName(), 'storage');
        $this->dropColumn(ReportAttachmentOriginal::tableName(), 'storage');
    }
}
