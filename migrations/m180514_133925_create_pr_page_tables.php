<?php

use yii\db\Migration;

class m180514_133925_create_pr_page_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('pr_page', [
            'id' => $this->bigPrimaryKey(),
            'institution_id' => $this->bigInteger()->notNull(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(1),
            'title' => $this->string()->notNull(),
            'info_web_page' => $this->string()->defaultValue(null),
            'info_email' => $this->string()->defaultValue(null),
            'info_phone' => $this->string()->defaultValue(null),
            'info_address' => $this->string()->defaultValue(null),
            'video_url' => $this->string()->defaultValue(null),
            'social_feed_url' => $this->string()->defaultValue(null),
            'custom_color' => $this->string()->defaultValue('#81bb41'),
            'cover_file_name' => $this->string()->defaultValue(null),
            'logo_file_name' => $this->string()->defaultValue(null),
            'introduction' => $this->text()->notNull(),
            'map_status' => $this->string()->notNull()->defaultValue(0),
            'slug' => $this->string()->defaultValue(null),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->addForeignKey('pr_page_FK_institution', 'pr_page', 'institution_id', 'institution', 'id', 'CASCADE');

        $this->createTable('admin_pr_page', [
            'pr_page_id' => $this->bigInteger()->notNull(),
            'admin_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),

            'PRIMARY KEY (`pr_page_id`, `admin_id`)',
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->addForeignKey('admin_pr_page_FK_pr_page', 'admin_pr_page', 'pr_page_id', 'pr_page', 'id');
        $this->addForeignKey('admin_pr_page_FK_admin', 'admin_pr_page', 'admin_id', 'admin', 'id');

        $this->createTable('pr_page_news', [
            'id' => $this->bigPrimaryKey(),
            'status' => $this->smallInteger()->notNull()->unsigned()->defaultValue(0),
            'highlighted' => $this->smallInteger()->notNull()->unsigned()->defaultValue(0),
            'pr_page_id' => $this->bigInteger()->notNull(),
            'title' => $this->string()->notNull(),
            'image_file_name' => $this->string()->defaultValue(null),
            'text' => $this->text()->notNull(),
            'published_at' => $this->bigInteger()->notNull()->unsigned(),
            'created_at' => $this->bigInteger()->notNull()->unsigned(),
            'updated_at' => $this->bigInteger()->notNull()->unsigned(),
        ], \app\components\helpers\Migration::TABLE_OPTIONS);

        $this->addForeignKey('pr_page_news_FK_pr_page', 'pr_page_news', 'pr_page_id', 'pr_page', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('pr_page_FK_institution', 'pr_page');
        $this->dropForeignKey('admin_pr_page_FK_pr_page', 'admin_pr_page');
        $this->dropForeignKey('admin_pr_page_FK_admin', 'admin_pr_page');
        $this->dropForeignKey('pr_page_news_FK_pr_page', 'pr_page_news');

        $this->dropTable('pr_page');
        $this->dropTable('admin_pr_page');
        $this->dropTable('pr_page_news');
    }
}
