<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admin_city".
 *
 * @property integer $admin_id
 * @property integer $city_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Admin $admin
 * @property City $city
 */
class AdminCity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_city';
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
            [['admin_id', 'city_id'], 'required'],
            [['admin_id', 'city_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'admin_id'   => Yii::t('data', 'admin_city.admin_id'),
            'city_id'    => Yii::t('data', 'admin_city.city_id'),
            'created_at' => Yii::t('data', 'admin_city.created_at'),
            'updated_at' => Yii::t('data', 'admin_city.updated_at'),
        ];
    }

    /**
     * The Admin relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * The City relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
