<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;

class StatCityForm extends Model
{
    /**
     * @var int
     */
    public $city_id;

    /**
     * @var int
     */
    public $days;

    /**
     * @var int
     */
    public $limit;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'days', 'limit'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city_id' => Yii::t('statistics', 'label.city'),
            'days' => Yii::t('statistics', 'label.days'),
            'limit' => Yii::t('statistics', 'label.limit'),
        ];
    }
}
