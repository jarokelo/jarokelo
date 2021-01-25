<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activity_cache".
 *
 * @property integer $id
 * @property integer $report_activity_id
 * @property integer $admin_id
 * @property string $created_at
 *
 * @property Admin $admin
 * @property ReportActivity $reportActivity
 */
class ActivityCache extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_cache';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'timestamp' => [
                    'class' => TimestampBehavior::class,
                    'updatedAtAttribute' => false,
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_activity_id', 'admin_id', 'created_at'], 'integer'],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['admin_id' => 'id']],
            [['report_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportActivity::className(), 'targetAttribute' => ['report_activity_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('activity_cache', 'ID'),
            'report_activity_id' => Yii::t('activity_cache', 'Report Activity ID'),
            'admin_id' => Yii::t('activity_cache', 'Admin ID'),
            'created_at' => Yii::t('activity_cache', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivity()
    {
        return $this->hasOne(ReportActivity::className(), ['id' => 'report_activity_id']);
    }
}
