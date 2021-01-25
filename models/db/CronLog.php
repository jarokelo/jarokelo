<?php

namespace app\models\db;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\StringHelper;
use yii\log\Logger;

/**
 * This is the model class for table "cron_job_log".
 *
 * @property integer $id
 * @property integer $type
 * @property string $output
 * @property string $error_message
 * @property integer $runtime
 * @property string $created_at
 * @property string $updated_at
 */
class CronLog extends \yii\db\ActiveRecord
{
    const TYPE_DOWNLOAD_EMAILS = 1;
    const TYPE_NOTIFICATION = 2;
    const TYPE_TIMEOUT_CHECK = 3;
    const TYPE_DAILY_MAIL = 4;

    /**
     * @var Logger
     */
    private $_logger;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    public function init()
    {
        parent::init();
        $this->output = '';
        $this->error_message = '';
        $this->_logger = Yii::getLogger();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setRuntimeByLogger']);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_job_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'runtime'], 'integer'],
            [['output', 'error_message'], 'string'],
            [['output', 'error_message'], 'atLeastOneRequired'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function types()
    {
        return [
            self::TYPE_DOWNLOAD_EMAILS => Yii::t('cron-log', 'type-download-emails'),
            self::TYPE_NOTIFICATION => Yii::t('cron-log', 'type-notification'),
            self::TYPE_TIMEOUT_CHECK => Yii::t('cron-log', 'type-timeout-check'),
        ];
    }

    public function atLeastOneRequired($attribute, $params)
    {
        if (empty($this->output) && empty($this->error_message)) {
            $this->addError('output', 'One field must be filled');
            $this->addError('error_message', 'One field must be filled');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('cron-log', 'id'),
            'type' => Yii::t('cron-log', 'type'),
            'output' => Yii::t('cron-log', 'output'),
            'error_message' => Yii::t('cron-log', 'error_message'),
            'runtime' => Yii::t('cron-log', 'runtime'),
            'created_at' => Yii::t('cron-log', 'created_at'),
            'updated_at' => Yii::t('cron-log', 'updated_at'),
        ];
    }

    /**
     * @param $message
     * @param bool $save
     */
    public function addErrorMessage($message, $save = false)
    {
        $this->output .= $message . "\n";
        $this->error_message .= $message . "\n";

        if ($save) {
            $this->save();
        }
    }

    /**
     * @param $message
     * @param bool $save
     */
    public function addOutput($message, $save = false)
    {
        $this->output .= $message . "\n";

        if ($save) {
            $this->save();
        }
    }

    public function setRuntimeByLogger()
    {
        $this->runtime = $this->_logger->getElapsedTime();
        $this->_logger->flush(true);
    }
}
