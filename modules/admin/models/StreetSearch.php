<?php

namespace app\modules\admin\models;

use app\models\db\Street;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search form for Streets.
 *
 * @package app\modules\admin\models
 */
class StreetSearch extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $district;

    /**
     * @var \app\models\db\City
     */
    public $city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
            ['district', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'     => Yii::t('street', 'search.name'),
            'district' => Yii::t('street', 'search.district'),
        ];
    }

    /**
     * Searches the database for Streets.
     *
     * @param array $queryParams The query parameters for filtering
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = Street::find()
            ->where(['city_id' => $this->city->id])
            ->with('district')
            ->groupBy('street.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'name',
                    'district',
                ],
            ],
        ]);

        if ($this->load($queryParams) && $this->validate()) {
            $query
                ->andFilterWhere(['LIKE', 'name', $this->name])
                ->andFilterWhere(['district_id' => $this->district]);
        }

        return $dataProvider;
    }

    /**
     * Returns the available Districts for the City.
     *
     * @return string[] The available Districts
     */
    public function getAvailableDistricts()
    {
        $ret = [];

        foreach ($this->city->districts as $district) {
            $ret[$district->id] = $district->name;
        }

        return $ret;
    }
}
