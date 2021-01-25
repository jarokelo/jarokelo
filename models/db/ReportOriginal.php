<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_original".
 *
 * @property integer $report_id
 * @property string $name
 * @property string $report_category_id
 * @property string $description
 * @property string $user_location
 * @property string $latitude
 * @property string $longitude
 * @property integer $zoom
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Report $report
 */
class ReportOriginal extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_original';
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
            [['report_id', 'name', 'report_category_id', 'user_location', 'latitude', 'longitude'], 'required'],
            [['report_id', 'zoom', 'created_at', 'updated_at', 'report_category_id'], 'integer'],
            [['description'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'user_location'], 'string', 'max' => 255],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Report::className(), 'targetAttribute' => ['report_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_id'          => Yii::t('data', 'report_original.report_id'),
            'name'               => Yii::t('data', 'report_original.name'),
            'report_category_id' => Yii::t('data', 'report_original.report_category_id'),
            'description'        => Yii::t('data', 'report_original.description'),
            'user_location'      => Yii::t('data', 'report_original.user_location'),
            'latitude'           => Yii::t('data', 'report_original.latitude'),
            'longitude'          => Yii::t('data', 'report_original.longitude'),
            'zoom'               => Yii::t('data', 'report_original.zoom'),
            'created_at'         => Yii::t('data', 'report_original.created_at'),
            'updated_at'         => Yii::t('data', 'report_original.updated_at'),
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
     * The ReportCategory relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportCategory()
    {
        return $this->hasOne(ReportCategory::className(), ['id' => 'report_category_id']);
    }
}
