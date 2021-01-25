<?php

namespace app\modules\admin\models;

use app\models\db\City;
use app\models\db\Institution;
use app\models\db\ReportCategory;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;

class StatisticsSearch extends Model
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
        $uniqueName = Yii::$app->params['report-unique-name'];

        $query = StatisticsReport::find()
            ->select([
                'report.*',
                'nameAndUniqueId' => new Expression("CONCAT(UPPER('{$uniqueName}'), '-', UPPER(`city`.`name`), '-', LPAD(`report`.`id`, 8, '0'))"),
                'cityName' => 'city.name',
                'reportCategoryName' => 'report_category.name',
                'institutionName' => 'institution.name',
            ])
            ->leftJoin(City::tableName(), 'city.id=report.city_id')
            ->leftJoin(ReportCategory::tableName(), 'report_category.id=report.report_category_id')
            ->leftJoin(Institution::tableName(), 'institution.id=report.institution_id');

        if (!($this->load($queryParams) && $this->validate())) {
            $this->start_date = date('Y-m-01');
            $this->end_date = date('Y-m-d');
        }

        $start_date = strtotime($this->start_date);
        $end_date = strtotime($this->end_date);

        $query->where(new Expression('report.created_at >= :start_date AND report.created_at <= :end_date', [
            ':start_date' => $start_date,
            ':end_date' => $end_date,
        ]));

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query->orderBy(['id' => SORT_ASC])->asArray()->all(),
            'pagination' => false,
        ]);

        return $dataProvider;
    }
}
