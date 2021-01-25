<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $report_id
 * @property string $send_date
 * @property string $sent_date
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Report $report
 * @property User $user
 * @property ReportActivity[] $reportActivities
 */
class Notification extends ActiveRecord
{
    const STATUS_CANCELLED = 0;
    const STATUS_WAITING   = 1;
    const STATUS_SENT      = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
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
            [['user_id', 'report_id'], 'required'],
            [['user_id', 'report_id', 'send_date', 'sent_date', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('data', 'notification.id'),
            'user_id'    => Yii::t('data', 'notification.user_id'),
            'report_id'  => Yii::t('data', 'notification.report_id'),
            'send_date'  => Yii::t('data', 'notification.send_date'),
            'sent_date'  => Yii::t('data', 'notification.sent_date'),
            'status'     => Yii::t('data', 'notification.status'),
            'created_at' => Yii::t('data', 'notification.created_at'),
            'updated_at' => Yii::t('data', 'notification.updated_at'),
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
     * The User relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['notification_id' => 'id']);
    }
}
