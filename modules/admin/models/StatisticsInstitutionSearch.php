<?php

namespace app\modules\admin\models;

use Yii;
use app\models\db\Report;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class StatisticsInstitutionSearch extends Model
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
        $query = StatisticsInstitution::find()
            ->select([
                'institution.name',
                'resolved' => new Expression('SUM(IF(r.status=:status_resolved, 1, 0))', [':status_resolved' => Report::STATUS_RESOLVED]),
                'unresolved' => new Expression('SUM(IF(r.status=:status_unresolved, 1, 0))', [':status_unresolved' => Report::STATUS_UNRESOLVED]),
                'waiting_for_response' => new Expression('SUM(IF(r.status=:status_wfr, 1, 0))', [':status_wfr' => Report::STATUS_WAITING_FOR_RESPONSE]),
                'waiting_for_solution' => new Expression('SUM(IF(r.status=:status_wfs, 1, 0))', [':status_wfs' => Report::STATUS_WAITING_FOR_SOLUTION]),
            ]);

        if (!($this->load($queryParams) && $this->validate())) {
            $this->start_date = date('Y-01-01');
            $this->end_date = date('Y-m-d');
        }

        $start_date = strtotime($this->start_date);
        $end_date = strtotime($this->end_date);

        $query
            ->leftJoin(['r' => Report::tableName()], 'institution.id = r.institution_id' . ($start_date ? ' AND r.created_at >= ' . $start_date : '') . ($end_date ? ' AND r.created_at <= ' . $end_date : ''));

        $query->groupBy('institution.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
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
