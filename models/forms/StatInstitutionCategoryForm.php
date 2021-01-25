<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;

class StatInstitutionCategoryForm extends Model
{
    public $limit;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['limit'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'limit' => Yii::t('statistics', 'label.limit'),
        ];
    }
}
