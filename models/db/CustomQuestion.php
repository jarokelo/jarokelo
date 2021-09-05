<?php

namespace app\models\db;

use Yii;
use yii\behaviors\BlameableBehavior;
use app\helpers\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "custom_questions".
 *
 * @property int $id
 * @property string $question
 * @property string $description
 * @property int $status
 * @property int $type
 * @property string $answer_options
 * @property int $required
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Admin $createdBy
 * @property Admin $updatedBy
 */
class CustomQuestion extends \yii\db\ActiveRecord
{
    const TYPE_LONG_TEXT_ANSWER = 1;
    const TYPE_RADIO_BUTTON = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_SINGLE_SELECT_DROPDOWN = 4;
    const TYPE_LINEAR_SCALE = 5;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const LONG_TEXT_MAX_LENGTH = 500;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'custom_questions';
    }

    /**
     * @return array
     */
    public static function getStatusSelection()
    {
        return [
            self::STATUS_INACTIVE => Yii::t('custom_question', 'Inaktív'),
            self::STATUS_ACTIVE => Yii::t('custom_question', 'Aktív'),
        ];
    }

    /**
     * @return array
     */
    public static function getRequiredSelection()
    {
        return [
            Yii::t('custom_question', 'Nem kötelező'),
            Yii::t('custom_question', 'Kötelező'),
        ];
    }

    /**
     * @return array
     */
    public static function getQuestionTypes()
    {
        return [
            self::TYPE_LONG_TEXT_ANSWER => Yii::t('custom_question', 'Szabad szöveg'),
            self::TYPE_RADIO_BUTTON => Yii::t('custom_question', 'Feleletválasztó (rádiógomb)'),
            self::TYPE_CHECKBOX => Yii::t('custom_question', 'Jelölőnégyzet'),
            self::TYPE_SINGLE_SELECT_DROPDOWN => Yii::t('custom_question', 'Legördülő lista'),
            self::TYPE_LINEAR_SCALE => Yii::t('custom_question', 'Lineáris skála'),
        ];
    }

    /**
     * Compiling a list of the active items
     *
     * @return array
     */
    public static function getList()
    {
        return array_reduce(
            static::find()
                ->where(
                    [
                        'status' => self::STATUS_ACTIVE,
                    ]
                )
                ->all(),
            function (array $carry, self $customQuestion) {
                $carry[$customQuestion->id] = $customQuestion->question;
                return $carry;
            },
            []
        );
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                BlameableBehavior::class,
                TimestampBehavior::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question', 'type'], 'required'],
            [['description'], 'string'],
            [['status', 'type', 'required', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'answer_options'], 'safe'],
            [['question'], 'string', 'max' => 255],
            [['question'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('custom_question', 'ID'),
            'question' => Yii::t('custom_question', 'Kérdés'),
            'description' => Yii::t('custom_question', 'Leírás'),
            'status' => Yii::t('custom_question', 'Státusz'),
            'type' => Yii::t('custom_question', 'Típus'),
            'answer_options' => Yii::t('custom_question', 'Válaszlehetőségek'),
            'required' => Yii::t('custom_question', 'Kötelező'),
            'created_at' => Yii::t('custom_question', 'Létrehozva'),
            'updated_at' => Yii::t('custom_question', 'Módosítva'),
            'created_by' => Yii::t('custom_question', 'Létrehozta'),
            'updated_by' => Yii::t('custom_question', 'Módosította'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // afterFind() method doesn't work on new model entities
        if (!$this->answer_options) {
            $this->answer_options = '[]';
        }
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $answers = Json::decode($this->answer_options);
        usort(
            $answers,
            function ($a, $b) {
                if (!isset($a['priority']) || !isset($b['priority'])) {
                    return;
                }

                // PHP 7 version - $a['priority'] <=> $b['priority]
                return ($a['priority'] < $b['priority'])
                    ? -1
                    : (($a['priority'] > $b['priority']) ? 1 : 0);
            }
        );

        $this->answer_options = Json::encode($answers);
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        if ($this->required == '0,1') {
            $this->required = '1';
        }

        // Removing empty responses
        $answerOptions = [];

        foreach ((array)$this->answer_options as $key => $answer) {
            if (is_array($answer)) {
                foreach ($answer as $index => $v) {
                    if (isset($v) && $v != '[]') {
                        $answerOptions[$key][$index] = $v;
                    }
                }
            }
        }

        $this->answer_options = $answerOptions;
        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $parent = parent::beforeSave($insert);

        if (!$this->answer_options) {
            $this->answer_options = '[]';

            if (!$this->answerValidator()) {
                return false;
            }

            return $parent;
        }

        $priority = 0;
        $answerOptions = [];

        // Receiving an array, saving a JSON encoded string
        foreach ((array)$this->answer_options as $i => $v) {
            // Should be saved in a different way
            if ($i == 'linear_scale' && $this->type == $this::TYPE_LINEAR_SCALE) {
                $answerOptions[] = $v;
                continue;
            }

            if ($i != 'text') {
                continue;
            }

            // Priority value to order the answer options
            foreach ($v as $value) {
                $answerOptions[] = compact('priority', 'value');
                $priority += 5;
            }
        }

        $this->answer_options = Json::encode($answerOptions);
        return $parent;
    }

    /**
     * @return bool
     */
    public function answerValidator()
    {
        if ((empty($this->answers) || $this->answers == '[]') && !in_array(
            $this->type,
            [
                self::TYPE_LONG_TEXT_ANSWER,
                self::TYPE_LINEAR_SCALE,
            ]
        )) {
            $this->addError(
                'type',
                Yii::t('custom_question', 'A válasz opciók megadása kötelező')
            );
            return false;
        }


        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Admin::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Admin::className(), ['id' => 'updated_by']);
    }
}
