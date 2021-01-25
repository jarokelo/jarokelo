<?php

namespace app\models\db;

use app\components\helpers\Link;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Ddeboer\Imap\Server;
use Yii;

use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $name
 * @property string $name_filter
 * @property string $slug
 * @property integer $has_districts
 * @property integer $status
 * @property string $latitude
 * @property string $longitude
 * @property string $created_at
 * @property string $updated_at
 * @property string $email_address
 * @property string $email_password
 *
 * @property AdminCity[] $adminCities
 * @property Admin[] $admins
 * @property District[] $districts
 * @property Institution[] $institutions
 * @property Report[] $reports
 * @property Rule[] $rules
 * @property Street[] $streets
 * @property StreetGroup[] $streetGroups
 * @property User[] $users
 */
class City extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const BUDAPEST = 1;

    public $districtCount;
    public $streetCount;
    public $reportCount;
    public $adminCount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
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
            [['name', 'name_filter', 'status', 'latitude', 'longitude', 'email_address'], 'required'],
            [['has_districts'], 'required', 'on' => self::SCENARIO_CREATE],
            [['has_districts', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'name_filter', 'slug', 'email_password'], 'string', 'max' => 255],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'unique'],
            [['slug'], 'unique'],
            [['email_address'], 'email'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'city.id'),
            'name' => Yii::t('data', 'city.name'),
            'name_filter' => Yii::t('data', 'city.name_filter'),
            'has_districts' => Yii::t('data', 'city.has_districts'),
            'status' => Yii::t('data', 'city.status'),
            'created_at' => Yii::t('data', 'city.created_at'),
            'updated_at' => Yii::t('data', 'city.updated_at'),
            'email_address' => Yii::t('data', 'city.email_address'),
            'email_password' => Yii::t('data', 'city.email_password'),
        ];
    }

    /**
     * Returns the available statuses a City can have.
     *
     * @return string[]
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('city', 'status.active'),
            self::STATUS_INACTIVE => Yii::t('city', 'status.inactive'),
        ];
    }

    /**
     * Returns the translated value for has_district field.
     *
     * @return string[]
     */
    public static function hasDistrict()
    {
        return [
            1 => Yii::t('label', 'generic.yes'),
            0 => Yii::t('label', 'generic.no'),
        ];
    }

    /**
     * The AdminCities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminCities()
    {
        return $this->hasMany(AdminCity::className(), ['city_id' => 'id']);
    }

    /**
     * The Admins relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(Admin::className(), ['id' => 'admin_id'])->viaTable(AdminCity::tableName(), ['city_id' => 'id']);
    }

    /**
     * The Districts relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(District::className(), ['city_id' => 'id']);
    }

    /**
     * The Institutions relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['city_id' => 'id']);
    }

    /**
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['city_id' => 'id']);
    }

    /**
     * The Rules relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['city_id' => 'id']);
    }

    /**
     * The Streets relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreets()
    {
        return $this->hasMany(Street::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreetGroups()
    {
        return $this->hasMany(StreetGroup::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['city_id' => 'id']);
    }

    /**
     * Returns the available Cities' list.
     *
     * @param boolean $onlyActive If true, only Cities with active status will be listed
     * @param boolean $adminCheck if true, the City list will be limited by the Admin's permission
     * @return string[] The City's name in an array, with the City id as key
     */
    public static function availableCities($onlyActive = false, $adminCheck = true)
    {
        $ret = [];

        $query = static::find()->filterWhere(['status' => $onlyActive ? self::STATUS_ACTIVE : null]);

        if ($adminCheck) {
            $query
                ->leftJoin(AdminCity::tableName(), 'admin_city.city_id = city.id')
                ->andWhere(['admin_city.admin_id' => Yii::$app->user->id]);
        }

        /* @var \app\models\db\City[] $cities */
        $cities = $query->orderBy('name ASC')->all();
        foreach ($cities as $city) {
            $ret[$city->id] = $city->name;
        }

        return $ret;
    }

    /**
     * Returns the available Cities' list.
     *
     * @param boolean $onlyActive If true, only Cities with active status will be listed
     * @return string[] The City's name in an array, with the City id as key
     */
    public static function getAllForFilter($onlyActive = false)
    {
        $ret = [];

        $query = static::find()->filterWhere(['status' => $onlyActive ? self::STATUS_ACTIVE : null]);

        /* @var \app\models\db\City[] $cities */
        $cities = $query->orderBy('name_filter ASC')->all();

        return ArrayHelper::map($cities, 'id', 'name_filter');
    }

    /**
     * Returns the array of the available Districts.
     *
     * @return string[] The District's name in an array, with the District's id as key
     */
    public function getAvailableDistricts()
    {
        $districts = [];

        foreach ($this->districts as $district) {
            $districts[$district->id] = $district->name;
        }

        return $districts;
    }

    /**
     * Returns the array of the available Streets.
     *
     * @return string[] The Street's name in an array, with the Street's id as key
     */
    public function getAvailableStreets()
    {
        $streets = [];

        foreach ($this->streets as $street) {
            $streets[$street->id] = $street->name;
        }

        return $streets;
    }

    /**
     * Return the array of the District numbers.
     *
     * @return array[] The District's id and number in an array
     */
    public function getDistrictNumbers()
    {
        $numbers = [];

        foreach ($this->districts as $district) {
            $numbers[] = [
                'district_id' => $district->id,
                'number' => $district->number,
            ];
        }

        return $numbers;
    }

    /**
     * Returns the city ID by slug or false
     *
     * @param string $slug
     *
     * @return bool|string
     */
    public static function getIdBySlug($slug)
    {
        return static::getDb()->cache(function ($db) use ($slug) {
            return static::find()->select('id')->where(['slug' => $slug])->scalar();
        }, Yii::$app->params['cache']['db']['cityIdBySlug']);
    }

    /**
     * Returns the city slug by ID or false
     *
     * @param string $id
     *
     * @return bool|string
     */
    public static function getSlugById($id)
    {
        return static::getDb()->cache(function ($db) use ($id) {
            return static::find()->select('slug')->where(['id' => $id])->scalar();
        }, Yii::$app->params['cache']['db']['citySlugById']);
    }

    /**
     * Returns the valid statistic url for a city
     *
     * @param $cityId
     * @param array $prePath [optional]
     *
     * @return bool|string
     */
    public static function getStatisticUrl($cityId, $prePath = [])
    {
        $city = static::findOne($cityId);

        if ($city === null) {
            return false;
        }

        return Link::to(array_merge($prePath, [$city->slug]));
    }

    /**
     * Returns the closest city to the given lat, long.
     *
     * @param $latitude
     * @param $longitude
     *
     * @return bool|string
     */
    public static function getNearestCity($latitude, $longitude)
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        // TODO: do it in DB search to avoid big data movement when more cities would be added
        $query = static::find()->filterWhere(['status' => self::STATUS_ACTIVE]);
        /* @var \app\models\db\City[] $cities */
        $cities = $query->orderBy('name ASC')->all();

        $nearestCity = false;
        $minimumDistance = null;
        foreach ($cities as $city) {
            $distance = ($city->latitude - $latitude) ** 2 + ($city->longitude - $longitude) ** 2;
            if ($minimumDistance === null || $distance < $minimumDistance) {
                $minimumDistance = $distance;
                $nearestCity = $city;
            }
        }

        return $nearestCity;
    }
}
