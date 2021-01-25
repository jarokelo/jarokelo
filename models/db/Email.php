<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "email".
 *
 * @property integer $id
 * @property integer $report_id
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $body
 * @property integer $direction
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Report $report
 * @property ReportActivity[] $reportActivities
 * @property ReportAttachment[] $reportAttachments
 */
class Email extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to', 'direction'], 'required'],
            [['report_id', 'direction', 'created_at', 'updated_at'], 'integer'],
            [['body'], 'string'],
            [['from', 'to', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('data', 'email.id'),
            'report_id'  => Yii::t('data', 'email.report_id'),
            'from'       => Yii::t('data', 'email.from'),
            'to'         => Yii::t('data', 'email.to'),
            'subject'    => Yii::t('data', 'email.subject'),
            'body'       => Yii::t('data', 'email.body'),
            'direction'  => Yii::t('data', 'email.direction'),
            'created_at' => Yii::t('data', 'email.created_at'),
            'updated_at' => Yii::t('data', 'email.updated_at'),
        ];
    }

    /**
     * The Report relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['email_id' => 'id']);
    }

    /**
     * The ReportAttachments relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachments()
    {
        return $this->hasMany(ReportAttachment::className(), ['email_id' => 'id']);
    }
}
