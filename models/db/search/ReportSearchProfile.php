<?php

namespace app\models\db\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\db\Report;

/**
 * ReportSearch represents the model behind the search form about `app\models\db\Report`.
 */
class ReportSearchProfile extends Report
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'trim'],
            [['status'], 'default'],
            [['status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Report::find()
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
                'totalCount' => $query->count(),
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'rule_id' => $this->rule_id,
            'institution_id' => $this->institution_id,
            'user_id' => $this->user_id,
            'admin_id' => $this->admin_id,
            'district_id' => $this->district_id,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zoom' => $this->zoom,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sent_email_count' => $this->sent_email_count,
            'highlighted' => $this->highlighted,
            'report_category_id' => $this->report_category_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'user_location', $this->user_location])
            ->andFilterWhere(['like', 'post_code', $this->post_code])
            ->andFilterWhere(['like', 'street_name', $this->street_name]);

        return $dataProvider;
    }
}
