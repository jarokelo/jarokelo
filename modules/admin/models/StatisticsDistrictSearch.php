<?php

namespace app\modules\admin\models;

use Yii;
use app\models\db\Report;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class StatisticsDistrictSearch extends Model
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'start_date' => Yii::t('data', 'report.search.start_date'),
            'end_date' => Yii::t('data', 'report.search.end_date'),
        ];
    }

    /**
     * @param array $queryParams
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = StatisticsDistrict::find()
            ->select([
                'id' => 'district.id',
                'district.name',
                'resolved' => new Expression('SUM(IF(r.status=:status1, 1, 0))', [':status1' => Report::STATUS_RESOLVED]),
                'unresolved' => new Expression('SUM(IF(r.status=:status2, 1, 0))', [':status2' => Report::STATUS_UNRESOLVED]),
                'waiting_for_response' => new Expression('SUM(IF(r.status=:status3, 1, 0))', [':status3' => Report::STATUS_WAITING_FOR_RESPONSE]),
                'waiting_for_solution' => new Expression('SUM(IF(r.status=:status4, 1, 0))', [':status4' => Report::STATUS_WAITING_FOR_SOLUTION]),
            ]);

        if (!($this->load($queryParams) && $this->validate())) {
            $this->start_date = date('Y-01-01');
            $this->end_date = date('Y-m-d');
        }

        $start_date = strtotime($this->start_date);
        $end_date = strtotime($this->end_date);

        $query
            ->leftJoin(['r' => Report::tableName()], 'district.id = r.district_id' . ($start_date ? ' AND r.created_at >= ' . $start_date : '') . ($end_date ? ' AND r.created_at <= ' . $end_date : ''));

        $query->groupBy('district.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
                'attributes' => [
                    'id',
                    'name',
                    'resolved',
                    'unresolved',
                    'waiting_for_response',
                    'waiting_for_solution',
                ],
            ],
        ]);

        return $dataProvider;
    }
}
