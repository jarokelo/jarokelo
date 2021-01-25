<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "email_log".
 *
 * @property integer $id
 * @property string $_get
 * @property string $_post
 * @property string $_server
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property integer $is_successful
 * @property integer $created_at
 */
class EmailLog extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'email_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_get', '_post', '_server'], 'string'],
            [['is_successful', 'created_at'], 'integer'],
            [['from', 'to', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('email_log', 'ID'),
            '_get' => Yii::t('email_log', 'Get'),
            '_post' => Yii::t('email_log', 'Post'),
            '_server' => Yii::t('email_log', 'Server'),
            'from' => Yii::t('email_log', 'From'),
            'to' => Yii::t('email_log', 'To'),
            'subject' => Yii::t('email_log', 'Subject'),
            'is_successful' => Yii::t('email_log', 'Is Successful'),
            'created_at' => Yii::t('email_log', 'Created At'),
        ];
    }
}
