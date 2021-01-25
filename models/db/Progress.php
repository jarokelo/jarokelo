<?php

namespace app\models\db;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use Yii;

/**
 * This is the model class for table "progress".
 *
 * @property integer $id
 * @property integer $amount
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Admin $admin
 */
class Progress extends \yii\db\ActiveRecord
{
    /**
     * 6M
     */
    const AMOUNT_REQUIRED = 6000000;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'progress';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'required'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'progress.model.id'),
            'amount' => Yii::t('admin', 'progress.model.amount'),
            'created_by' => Yii::t('admin', 'created_by'),
            'updated_by' => Yii::t('admin', 'updated_by'),
            'created_at' => Yii::t('admin', 'created_at'),
            'updated_at' => Yii::t('admin', 'updated_at'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Admin::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Admin::class, ['id' => 'updated_by']);
    }

    /**
     * @return int
     */
    public static function getAmountSum()
    {
        return static::find()->sum('amount');
    }
}
