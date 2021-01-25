<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "config".
 *
 * @property string $key
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class Config extends \yii\db\ActiveRecord
{
    const CATEGORY_GMAIL = 'gmail';
    const CATEGORY_AWS = 'aws';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'timestamp' => [
                    'class' => TimestampBehavior::class,
                    'createdAtAttribute' => 'created_at',
                    'updatedAtAttribute' => 'updated_at',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'created_at', 'updated_at'], 'required'],
            [['value'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['key'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => Yii::t('config', 'Key'),
            'value' => Yii::t('config', 'Value'),
            'created_at' => Yii::t('config', 'Created At'),
            'updated_at' => Yii::t('config', 'Updated At'),
        ];
    }
}
