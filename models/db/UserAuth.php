<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_auth".
 *
 * @property integer $user_id
 * @property string $source
 * @property integer $source_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAuth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id', 'source_id', 'created_at', 'updated_at'], 'integer'],
            [['source'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id'    => Yii::t('data', 'user_auth.user_id'),
            'source'     => Yii::t('data', 'user_auth.source'),
            'source_id'  => Yii::t('data', 'user_auth.source_id'),
            'created_at' => Yii::t('data', 'user_auth.created_at'),
            'updated_at' => Yii::t('data', 'user_auth.updated_at'),
        ];
    }

    /**
     * The User relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
