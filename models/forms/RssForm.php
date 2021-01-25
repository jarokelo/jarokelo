<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class RssForm extends Model
{
    /**
     * @var int
     */
    public $city;

    /**
     * @var int
     */
    public $district;

    /**
     * @var int
     */
    public $institution;

    /**
     * @var int
     */
    public $category;

    /**
     * @var int
     */
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'district', 'institution', 'category', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city' => Yii::t('rss', 'label.city'),
            'district' => Yii::t('rss', 'label.district'),
            'institution' => Yii::t('rss', 'label.institution'),
            'category' => Yii::t('rss', 'label.category'),
            'status' => Yii::t('rss', 'label.status'),
        ];
    }

    /**
     * Submits the contact form to the recipients.
     *
     * @return bool True, if the submit was successful
     */
    public function handleRss()
    {
        return true;
    }
}
