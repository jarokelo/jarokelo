<?php

namespace app\models\db;

use Yii;

/**
 * This is the model class for table "report_taxonomy_relation".
 *
 * @property int $report_category_id
 * @property int $report_taxonomy_id
 * @property int $priority
 *
 * @property ReportCategory $reportCategory
 * @property ReportTaxonomy $reportTaxonomy
 */
class ReportTaxonomyRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_taxonomy_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_category_id', 'report_taxonomy_id', 'priority'], 'required'],
            [['report_category_id', 'report_taxonomy_id', 'priority'], 'integer'],
            [['report_category_id', 'report_taxonomy_id'], 'unique', 'targetAttribute' => ['report_category_id', 'report_taxonomy_id']],
            [['report_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportCategory::className(), 'targetAttribute' => ['report_category_id' => 'id']],
            [['report_taxonomy_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportTaxonomy::className(), 'targetAttribute' => ['report_taxonomy_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'report_category_id' => Yii::t('report_taxonomy_relation', 'Report Category ID'),
            'report_taxonomy_id' => Yii::t('report_taxonomy_relation', 'Report Taxonomy ID'),
            'priority' => Yii::t('report_taxonomy_relation', 'Prioritás'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportCategory()
    {
        return $this->hasOne(ReportCategory::className(), ['id' => 'report_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportTaxonomy()
    {
        return $this->hasOne(ReportTaxonomy::className(), ['id' => 'report_taxonomy_id']);
    }
}
