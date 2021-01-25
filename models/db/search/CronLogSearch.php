<?php

namespace app\models\db\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\db\CronLog;

/**
 * CronLogSearch represents the model behind the search form about `app\models\db\CronLog`.
 */
class CronLogSearch extends CronLog
{
    /**
     * @var string
     */
    public $start_date;

    /**
     * @var string
     */
    public $end_date;
    /**
     * @var integer
     */
    public $has_error;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['id', 'type', 'created_at', 'updated_at', 'has_error'], 'integer'],
            [['output', 'error_message'], 'safe'],
            [['runtime'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'start_date' => Yii::t('data', 'report.search.start_date'),
            'end_date' => Yii::t('data', 'report.search.end_date'),
            'has_error' => Yii::t('cron-log', 'has_error'),
        ]);
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
        $query = CronLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!($this->load($params) && $this->validate())) {
            $this->start_date = date('Y-m-01');
            $this->end_date = date('Y-m-d');
        }

        $start_date = strtotime($this->start_date . ' 00:00:00');
        $end_date = strtotime($this->end_date . ' 23:59:59');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'runtime' => $this->runtime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andWhere(['BETWEEN', 'created_at', $start_date, $end_date]);

        if ($this->has_error) {
            $query->andWhere(['<>', 'error_message', '']);
        }

        $query->andFilterWhere(['like', 'output', $this->output])
            ->andFilterWhere(['like', 'error_message', $this->error_message]);

        return $dataProvider;
    }
}
