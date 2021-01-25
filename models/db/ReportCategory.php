<?php

namespace app\models\db;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "report_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 *
 * @property Report[] $reports
 * @property ReportOriginal[] $reportOriginals
 * @property Rule[] $rules
 */
class ReportCategory extends \yii\db\ActiveRecord
{
    const CATEGORY_OTHER = 17;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['is_active'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'category.id'),
            'name' => Yii::t('data', 'category.name'),
            'is_active' => Yii::t('data', 'category.is_active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['report_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportOriginals()
    {
        return $this->hasMany(ReportOriginal::className(), ['report_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['report_category_id' => 'id']);
    }

    /**
     * Returns a list of report categories in the following format:
     * [
     *  [1 => 'category1'],
     *  [2 => 'category2'],
     *  ...
     * ]
     *
     * Ideal for use as drop down list data source for example.
     *
     * @return array
     */
    public static function getList()
    {
        $result = static::getDb()->cache(function ($db) {
            return static::find()
                ->where(['is_active' => 1])
                ->orderBy('name')
                ->all();
        }, Yii::$app->params['cache']['db']['generalDbQuery']);

        return ArrayHelper::map($result, 'id', 'name');
    }

    /**
     * Returns the Report Category statistics.
     *
     * @param int $cityId [optional]
     * @param int $institutionId [optional]
     * @param int $limit [optional]
     *
     * @return array
     */
    public static function getStatistics($cityId = null, $institutionId = null, $limit = 10)
    {
        return static::find()
            ->select([
                'id' => 'report_category.id',
                'name' => 'report_category.name',
                'repCount' => new Expression('SUM(IF(report.status NOT IN (:status0,:status1,:status9,:status7), 1, 0))', [
                    ':status0' => Report::STATUS_NEW,
                    ':status1' => Report::STATUS_EDITING,
                    ':status2' => Report::STATUS_WAITING_FOR_INFO,
                    ':status3' => Report::STATUS_WAITING_FOR_ANSWER,
                    ':status4' => Report::STATUS_WAITING_FOR_RESPONSE,
                    ':status5' => Report::STATUS_RESOLVED,
                    ':status6' => Report::STATUS_UNRESOLVED,
                    ':status7' => Report::STATUS_DELETED,
                    ':status8' => Report::STATUS_WAITING_FOR_SOLUTION,
                    ':status9' => Report::STATUS_DRAFT,
                ]),
                'inprogress' => new Expression('SUM(IF(report.status IN(:status2,:status3,:status4,:status8), 1, 0))'),
                'resolved' => new Expression('SUM(IF(report.status=:status5, 1, 0))'),
                'unresolved' => new Expression('SUM(IF(report.status=:status6, 1, 0))'),
            ])
            ->leftJoin(Report::tableName(), 'report.report_category_id=report_category.id')
            ->andFilterWhere(['report.city_id' => $cityId])
            ->andFilterWhere(['report.institution_id' => $institutionId])
            ->groupBy(['report_category.id'])
            ->orderBy(['repCount' => SORT_DESC])
            ->limit($limit)
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }
}
