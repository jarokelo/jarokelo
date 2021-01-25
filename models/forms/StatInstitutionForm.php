<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;

class StatInstitutionForm extends Model
{
    /**
     * @var int
     */
    public $institution_id;

    /**
     * @var int
     */
    public $days;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institution_id', 'days'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'institution_id' => Yii::t('statistics', 'label.institution'),
            'days' => Yii::t('statistics', 'label.days'),
        ];
    }
}
