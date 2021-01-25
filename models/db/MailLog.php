<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mail_log".
 *
 * @property integer $id
 * @property integer $type
 * @property string $type_info
 * @property integer $user_id
 * @property integer $sent_at
 */
class MailLog extends \yii\db\ActiveRecord
{
    const TYPE_DAILY = 1;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'sent_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mail_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'user_id', 'sent_at'], 'integer'],
            [['type_info'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('mail_log', 'id'),
            'type' => Yii::t('mail_log', 'type'),
            'type_info' => Yii::t('mail_log', 'type_info'),
            'user_id' => Yii::t('mail_log', 'user_id'),
            'sent_at' => Yii::t('mail_log', 'sent_at'),
        ];
    }
}
