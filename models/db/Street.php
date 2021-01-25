<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "street".
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $district_id
 * @property string $name
 * @property string $latitude
 * @property string $longitude
 * @property string $created_at
 * @property string $updated_at
 *
 * @property City $city
 * @property District $district
 */
class Street extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'street';
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
            [['city_id', 'name', 'latitude', 'longitude'], 'required'],
            [['city_id', 'district_id', 'created_at', 'updated_at'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'street.id'),
            'city_id' => Yii::t('data', 'street.city_id'),
            'district_id' => Yii::t('data', 'street.district_id'),
            'name' => Yii::t('data', 'street.name'),
            'latitude' => Yii::t('data', 'street.latitude'),
            'longitude' => Yii::t('data', 'street.longitude'),
            'created_at' => Yii::t('data', 'street.created_at'),
            'updated_at' => Yii::t('data', 'street.updated_at'),
        ];
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

    /**
     * The District relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    /**
     *
     * @param int $cityId [optional]
     *
     * @return array
     */
    public static function listStreets($cityId = null)
    {
        $streets = [];
        $query = static::find()->with('district');

        if ($cityId !== null) {
            $query->andWhere(['city_id' => $cityId]);
        }

        /** @var \app\models\db\Street $street */
        foreach ($query->all() as $street) {
            $district = '';
            if ($street->district != null) {
                $district = ' (' . $street->district->name . ')';
            }
            $streets[$street->id] = $street->name . $district;
        }

        return $streets;
    }

    /**
     * The Followed reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreetGroups()
    {
        return $this->hasMany(StreetGroup::className(), ['id' => 'street_group_id'])
            ->viaTable('street_group__street', ['street_id' => 'id']);
    }
}
