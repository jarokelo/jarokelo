<?php

namespace app\modules\admin\models;

use Yii;

use app\models\db\StreetGroup;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search form for Streets.
 *
 * @package app\modules\admin\models
 */
class StreetGroupSearch extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('street', 'streetgroup.search.name'),
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
        $query = StreetGroup::find();

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
            $query->andFilterWhere(['LIKE', 'name', $this->name]);
        }

        return $dataProvider;
    }
}
