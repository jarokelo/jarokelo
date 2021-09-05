<?php

namespace app\models\db;

use Yii;

/**
 * This is the model class for table "report_answers".
 *
 * @property int $id
 * @property int $report_id
 * @property int $report_category_id
 * @property int $report_taxonomy_id
 * @property int $custom_form_id
 * @property string $answers
 * @property int $custom_question_id
 *
 * @property CustomForm $customForm
 * @property ReportCategory $reportCategory
 * @property Report $report
 * @property ReportTaxonomy $reportTaxonomy
 * @property CustomQuestion $customQuestion
 */
class ReportAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_id', 'report_category_id', 'custom_form_id', 'answers', 'custom_question_id'], 'required'],
            [['report_id', 'report_category_id', 'report_taxonomy_id', 'custom_form_id', 'custom_question_id'], 'integer'],
            [['answers'], 'string'],
            [['custom_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomForm::className(), 'targetAttribute' => ['custom_form_id' => 'id']],
            [['report_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportCategory::className(), 'targetAttribute' => ['report_category_id' => 'id']],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Report::className(), 'targetAttribute' => ['report_id' => 'id']],
            [['report_taxonomy_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportTaxonomy::className(), 'targetAttribute' => ['report_taxonomy_id' => 'id']],
            [['custom_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomQuestion::className(), 'targetAttribute' => ['custom_question_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('report_answers', 'ID'),
            'report_id' => Yii::t('report_answers', 'Report ID'),
            'report_category_id' => Yii::t('report_answers', 'Report Category ID'),
            'report_taxonomy_id' => Yii::t('report_answers', 'Report Taxonomy ID'),
            'custom_form_id' => Yii::t('report_answers', 'Custom Form ID'),
            'answers' => Yii::t('report_answers', 'Answers'),
            'custom_question_id' => Yii::t('report_answers', 'Custom Question ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomForm()
    {
        return $this->hasOne(CustomForm::className(), ['id' => 'custom_form_id']);
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
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportTaxonomy()
    {
        return $this->hasOne(ReportTaxonomy::className(), ['id' => 'report_taxonomy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomQuestion()
    {
        return $this->hasOne(CustomQuestion::className(), ['id' => 'custom_question_id']);
    }
}
