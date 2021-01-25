<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "report_event".
 *
 * @property integer $id
 * @property integer $report_id
 * @property integer $created_at
 * @property integer $source
 *
 * @property Report $report
 */
class ReportEvent extends \yii\db\ActiveRecord
{
    /**
     * Report sent by web app
     * @var int
     */
    const SOURCE_WEB = 1;

    /**
     * Report sent by API
     * @var int
     */
    const SOURCE_API = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_event';
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
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_id', 'source'], 'required'],
            [['report_id', 'created_at', 'source'], 'integer'],
            [['report_id'], 'unique'],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Report::className(), 'targetAttribute' => ['report_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->source = $this->source ?: self::SOURCE_WEB;
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('report_event', 'ID'),
            'report_id' => Yii::t('report_event', 'Report ID'),
            'created_at' => Yii::t('report_event', 'Created At'),
            'source' => Yii::t('report_event', 'Source'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }
}
