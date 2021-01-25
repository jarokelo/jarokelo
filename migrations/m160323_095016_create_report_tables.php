<?php

use yii\db\Migration;

class m160323_095016_create_report_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('report', [
            'id' => $this->bigPrimaryKey(),
            'city_id' => $this->bigInteger()->notNull(),
            'rule_id' => $this->bigInteger(),
            'institution_id' => $this->bigInteger(),
            'user_id' => $this->bigInteger()->notNull(),
            'admin_id' => $this->bigInteger(),
            'district_id' => $this->bigInteger(),
            'name' => $this->string()->notNull(),
            'category' => $this->string()->notNull(),
            'description' => $this->text(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'user_location' => $this->string()->notNull(),
            'latitude' => $this->decimal(10, 8)->notNull(),
            'longitude' => $this->decimal(11, 8)->notNull(),
            'zoom' => $this->smallInteger()->notNull()->unsigned()->defaultValue(5),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('report_activity', [
            'id' => $this->bigPrimaryKey(),
            'report_id' => $this->bigInteger(),
            'admin_id' => $this->bigInteger(),
            'user_id' => $this->bigInteger(),
            'institution_id' => $this->bigInteger(),
            'attachment_id' => $this->bigInteger(),
            'notification_id' => $this->bigInteger(),
            'email_id' => $this->bigInteger(),
            'type' => $this->string()->notNull(),
            'comment' => $this->text(),
            'comment_shown' => $this->boolean()->notNull()->defaultValue(1),
            'original_value' => $this->text(),
            'new_value' => $this->text(),
            'notify_date' => $this->bigInteger()->unsigned(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('report_attachment', [
            'id' => $this->bigPrimaryKey(),
            'report_id' => $this->bigInteger(),
            'email_id' => $this->bigInteger(),
            'type' => $this->smallInteger()->notNull()->unsigned(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'url' => $this->text(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('notification', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'report_id' => $this->bigInteger()->notNull(),
            'send_date' => $this->bigInteger()->unsigned(),
            'sent_date' => $this->bigInteger()->unsigned(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->createTable('email', [
            'id' => $this->bigPrimaryKey(),
            'report_id' => $this->bigInteger(),
            'from' => $this->string()->notNull(),
            'to' => $this->string()->notNull(),
            'subject' => $this->string()->notNull(),
            'body' => $this->text()->notNull(),
            'direction' => $this->smallInteger()->notNull()->unsigned(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->addColumn('rule', 'street_id', $this->bigInteger());

        $this->createIndex('report_index_city', 'report', 'city_id');
        $this->createIndex('report_index_rule', 'report', 'rule_id');
        $this->createIndex('report_index_institution', 'report', 'institution_id');
        $this->createIndex('report_index_user', 'report', 'user_id');
        $this->createIndex('report_index_admin', 'report', 'admin_id');
        $this->createIndex('report_index_district', 'report', 'district_id');
        $this->createIndex('report_index_category', 'report', 'category');
        $this->createIndex('report_index_status', 'report', 'status');
        $this->createIndex('report_index_updated', 'report', 'updated_at');
        $this->createIndex('report_activity_index_report', 'report_activity', 'report_id');
        $this->createIndex('report_activity_index_admin', 'report_activity', 'admin_id');
        $this->createIndex('report_activity_index_user', 'report_activity', 'user_id');
        $this->createIndex('report_activity_index_institution', 'report_activity', 'institution_id');
        $this->createIndex('report_activity_index_attachment', 'report_activity', 'attachment_id');
        $this->createIndex('report_activity_index_notification', 'report_activity', 'notification_id');
        $this->createIndex('report_activity_index_email', 'report_activity', 'email_id');
        $this->createIndex('report_activity_index_type', 'report_activity', 'type');
        $this->createIndex('report_activity_index_updated', 'report_activity', 'updated_at');
        $this->createIndex('report_attachment_index_email', 'report_attachment', 'email_id');
        $this->createIndex('report_attachment_index_type', 'report_attachment', 'type');
        $this->createIndex('report_attachment_index_report_status', 'report_attachment', ['report_id', 'status']);
        $this->createIndex('notification_index_user', 'notification', 'user_id');
        $this->createIndex('notification_index_report', 'notification', 'report_id');
        $this->createIndex('notification_index_date_status', 'notification', ['status', 'send_date', 'sent_date']);
        $this->createIndex('email_index_report', 'email', 'report_id');

        $this->addForeignKey('report_FK_city', 'report', 'city_id', 'city', 'id');
        $this->addForeignKey('report_FK_rule', 'report', 'rule_id', 'rule', 'id');
        $this->addForeignKey('report_FK_institution', 'report', 'institution_id', 'institution', 'id');
        $this->addForeignKey('report_FK_user', 'report', 'user_id', 'user', 'id');
        $this->addForeignKey('report_FK_admin', 'report', 'admin_id', 'admin', 'id');
        $this->addForeignKey('report_FK_district', 'report', 'district_id', 'district', 'id');
        $this->addForeignKey('report_activity_FK_report', 'report_activity', 'report_id', 'report', 'id');
        $this->addForeignKey('report_activity_FK_admin', 'report_activity', 'admin_id', 'admin', 'id');
        $this->addForeignKey('report_activity_FK_user', 'report_activity', 'user_id', 'user', 'id');
        $this->addForeignKey('report_activity_FK_institution', 'report_activity', 'institution_id', 'institution', 'id');
        $this->addForeignKey('report_activity_FK_attachment', 'report_activity', 'attachment_id', 'report_attachment', 'id');
        $this->addForeignKey('report_activity_FK_notification', 'report_activity', 'notification_id', 'notification', 'id');
        $this->addForeignKey('report_activity_FK_email', 'report_activity', 'email_id', 'email', 'id');
        $this->addForeignKey('report_attachment_FK_report', 'report_attachment', 'report_id', 'report', 'id');
        $this->addForeignKey('report_attachment_FK_email', 'report_attachment', 'email_id', 'email', 'id');
        $this->addForeignKey('notification_FK_user', 'notification', 'user_id', 'user', 'id');
        $this->addForeignKey('notification_FK_report', 'notification', 'report_id', 'report', 'id');
        $this->addForeignKey('email_FK_report', 'email', 'report_id', 'report', 'id');
        $this->addForeignKey('rule_FK_street', 'rule', 'street_id', 'street', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('report_FK_city', 'report');
        $this->dropForeignKey('report_FK_rule', 'report');
        $this->dropForeignKey('report_FK_institution', 'report');
        $this->dropForeignKey('report_FK_user', 'report');
        $this->dropForeignKey('report_FK_admin', 'report');
        $this->dropForeignKey('report_FK_district', 'report');
        $this->dropForeignKey('report_activity_FK_report', 'report_activity');
        $this->dropForeignKey('report_activity_FK_admin', 'report_activity');
        $this->dropForeignKey('report_activity_FK_user', 'report_activity');
        $this->dropForeignKey('report_activity_FK_institution', 'report_activity');
        $this->dropForeignKey('report_activity_FK_attachment', 'report_activity');
        $this->dropForeignKey('report_activity_FK_notification', 'report_activity');
        $this->dropForeignKey('report_activity_FK_email', 'report_activity');
        $this->dropForeignKey('report_attachment_FK_report', 'report_attachment');
        $this->dropForeignKey('report_attachment_FK_email', 'report_attachment');
        $this->dropForeignKey('notification_FK_user', 'notification');
        $this->dropForeignKey('notification_FK_report', 'notification');
        $this->dropForeignKey('email_FK_report', 'email');
        $this->dropForeignKey('rule_FK_street', 'rule');

        $this->dropIndex('report_index_city', 'report');
        $this->dropIndex('report_index_rule', 'report');
        $this->dropIndex('report_index_institution', 'report');
        $this->dropIndex('report_index_user', 'report');
        $this->dropIndex('report_index_admin', 'report');
        $this->dropIndex('report_index_district', 'report');
        $this->dropIndex('report_index_category', 'report');
        $this->dropIndex('report_index_status', 'report');
        $this->dropIndex('report_index_updated', 'report');
        $this->dropIndex('report_activity_index_report', 'report_activity');
        $this->dropIndex('report_activity_index_admin', 'report_activity');
        $this->dropIndex('report_activity_index_user', 'report_activity');
        $this->dropIndex('report_activity_index_institution', 'report_activity');
        $this->dropIndex('report_activity_index_attachment', 'report_activity');
        $this->dropIndex('report_activity_index_notification', 'report_activity');
        $this->dropIndex('report_activity_index_email', 'report_activity');
        $this->dropIndex('report_activity_index_type', 'report_activity');
        $this->dropIndex('report_activity_index_updated', 'report_activity');
        $this->dropIndex('report_attachment_index_email', 'report_attachment');
        $this->dropIndex('report_attachment_index_type', 'report_attachment');
        $this->dropIndex('report_attachment_index_report_status', 'report_attachment');
        $this->dropIndex('notification_index_user', 'notification');
        $this->dropIndex('notification_index_report', 'notification');
        $this->dropIndex('notification_index_date_status', 'notification');
        $this->dropIndex('email_index_report', 'email');

        $this->dropTable('report');
        $this->dropTable('report_activity');
        $this->dropTable('report_attachment');
        $this->dropTable('notification');
        $this->dropTable('email');

        $this->dropColumn('rule', 'street_id');
    }
}
