<?php

namespace app\models\db;

use Yii;
use app\models\db\User;
use app\models\db\ReportActivity;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_activity_ratings".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $activity_id
 * @property string $created_at
 * @property integer $state
 *
 * @property ReportActivity $activity
 * @property User $user
 */
class ReportActivityRatings extends ActiveRecord
{
    const FORM_TYPE_SIDEBAR = 'sidebar';
    const FORM_TYPE_MODAL = 'modal';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_activity_ratings';
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
            [['user_id', 'activity_id', 'state'], 'required'],
            [['user_id', 'activity_id', 'created_at', 'state'], 'integer'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'report_activity_ratings.id'),
            'user_id' => Yii::t('data', 'report_activity_ratings.user_id'),
            'activity_id' => Yii::t('data', 'report_activity_ratings.activity_id'),
            'created_at' => Yii::t('data', 'report_activity_ratings.created_at'),
            'state' => Yii::t('data', 'report_activity_ratings.state'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(ReportActivity::className(), ['id' => 'activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function getRatings($activityId, $state = null)
    {
        return static::find()->where(['activity_id' => $activityId])->andFilterWhere(['state' => $state])->count();
    }
}
