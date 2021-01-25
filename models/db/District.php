<?php

namespace app\models\db;

use Yii;

use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "district".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $name
 * @property string $name_filter
 * @property string $slug
 * @property string $short_name
 * @property integer $number
 * @property string $latitude
 * @property string $longitude
 * @property string $created_at
 * @property string $updated_at
 *
 * @property City $city
 * @property Report[] $reports
 * @property Rule[] $rules
 * @property Street[] $streets
 * @property User[] $users
 */
class District extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'district';
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
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'name', 'name_filter', 'short_name', 'number', 'latitude', 'longitude'], 'required'],
            [['city_id', 'created_at', 'updated_at', 'number'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'article', 'name_filter', 'short_name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'district.id'),
            'city_id' => Yii::t('data', 'district.city_id'),
            'name' => Yii::t('data', 'district.name'),
            'article' => Yii::t('data', 'district.article'),
            'name_filter' => Yii::t('data', 'district.name_filter'),
            'short_name' => Yii::t('data', 'district.short_name'),
            'number' => Yii::t('data', 'district.number'),
            'latitude' => Yii::t('data', 'district.latitude'),
            'longitude' => Yii::t('data', 'district.longitude'),
            'created_at' => Yii::t('data', 'district.created_at'),
            'updated_at' => Yii::t('data', 'district.updated_at'),
        ];
    }

    public static function getDistrictById($id)
    {
        return static::getDb()->cache(function ($db) use ($id) {
            return static::find()->where(['id' => $id])->one();
        }, Yii::$app->params['cache']['db']['cityIdBySlug']);
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
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['district_id' => 'id']);
    }

    /**
     * The Rules relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['district_id' => 'id']);
    }

    /**
     * The Streets relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreets()
    {
        return $this->hasMany(Street::className(), ['district_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['district_id' => 'id']);
    }

    /**
     * Returns the Street count.
     *
     * @return integer The count of Streets.
     */
    public function getStreetCount()
    {
        return count($this->streets);
    }

    /**
     * Returns the Report count.
     *
     * @return integer The count of Reports.
     */
    public function getReportCount()
    {
        return count($this->reports);
    }

    /**
     * Returns the available Cities' list.
     *
     * @static
     *
     * @param integer $cityId [optional] The City id where the districts are located
     *
     * @return array The District's name in an array, with the District id as key
     */
    public static function getAll($cityId = null, $nameAttribute = 'name')
    {
        $ret = [];

        /** @var \app\models\db\District[] $query */
        $query = static::find()->filterWhere(['city_id' => $cityId])->asArray()->all();

        return ArrayHelper::map($query, 'id', $nameAttribute);
    }

    /**
     * Returns the district ID by slug or false
     *
     * @param string $slug
     *
     * @return bool|string
     */
    public static function getIdBySlug($slug)
    {
        return static::find()->select('id')->where(['slug' => $slug])->scalar();
    }
}
