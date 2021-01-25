<?php

namespace app\modules\admin\models;

use app\models\db\AdminCity;
use app\models\db\City;

use app\models\db\District;
use app\models\db\Report;
use app\models\db\Street;
use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Search form for Cities.
 *
 * @package app\modules\admin\models
 */
class CitySearch extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
            ['status', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'   => Yii::t('city', 'search.name'),
            'status' => Yii::t('city', 'search.status'),
        ];
    }

    /**
     * Searches the database for Cities.
     *
     * @param array $queryParams The query parameters for filtering
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = City::find()
            ->select([
                'city.*',
                'districtCount' => new Expression('COUNT(distinct district.id)'),
                'streetCount' => new Expression('COUNT(distinct street.id)'),
                'reportCount' => new Expression(0), // too slow 'COUNT(distinct report.id)'
                'adminCount' => new Expression('COUNT(distinct admin_city.admin_id)'),
            ])
            ->join('LEFT JOIN', District::tableName(), 'district.city_id=city.id')
            ->join('LEFT JOIN', Street::tableName(), 'street.city_id=city.id')
            // ->join('LEFT JOIN', Report::tableName(), 'report.city_id=city.id')
            ->join('LEFT JOIN', AdminCity::tableName(), 'admin_city.city_id=city.id')
            ->groupBy('city.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'name',
                ],
            ],
        ]);

        if ($this->load($queryParams) && $this->validate()) {
            $query
                ->andFilterWhere(['LIKE', 'city.name', $this->name])
                ->andFilterWhere(['city.status' => $this->status]);
        }

        return $dataProvider;
    }
}
