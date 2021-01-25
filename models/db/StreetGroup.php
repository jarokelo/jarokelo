<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "street".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $city_id
 */
class StreetGroup extends ActiveRecord
{
    /**
     * @var array
     */
    public $connectedStreets;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'street_group';
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
            [['name', 'city_id'], 'required'],
            [['created_at', 'updated_at', 'city_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['connectedStreets'], 'safe'],
            [['name'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'street_group.id'),
            'name' => Yii::t('data', 'street_group.name'),
            'created_at' => Yii::t('data', 'street_group.created_at'),
            'updated_at' => Yii::t('data', 'street_group.updated_at'),
            'city_id' => Yii::t('data', 'street_group.city_id'),
            'connectedStreets' => Yii::t('data', 'street_group.connected_streets'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['street_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * Returns a list of all StreetGroups.
     *
     * @param int $cityId [optional]
     *
     * @return array
     */
    public static function getList($cityId = null)
    {
        $names = [];
        $query = static::find();

        if ($cityId !== null) {
            $query->andWhere(['city_id' => $cityId]);
        }

        /** @var \app\models\db\StreetGroup $group */
        foreach ($query->all() as $group) {
            $names[$group->id] = $group->name;
        }

        return $names;
    }

    /**
     * The Following users relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreets()
    {
        return $this->hasMany(Street::className(), ['id' => 'street_id'])
            ->viaTable('street_group__street', ['street_group_id' => 'id']);
    }
}
