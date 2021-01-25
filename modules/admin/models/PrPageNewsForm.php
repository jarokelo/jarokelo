<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.18.
 * Time: 10:38
 */

namespace app\modules\admin\models;

use Yii;
use app\models\db\PrPageNews;

class PrPageNewsForm extends PrPageNews
{
    /**
     * @var string
     */
    public $published_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pr_page_id', 'published_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['published_date', 'title', 'image_file_name', 'text'], 'string'],
            [['title', 'text'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'published_date' => Yii::t('pr_page_news', 'update.published_at'),
        ]);
    }
}
