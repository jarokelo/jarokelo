<?php

namespace app\models\db;

use app\helpers\behaviors\TimestampBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $custom_questions
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Admin $createdBy
 * @property Admin $updatedBy
 */
class CustomForm extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    /**
     * @var array
     */
    public $customQuestions = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'custom_forms';
    }

    /**
     * @return array
     */
    public static function getStatusSelection()
    {
        return [
            self::STATUS_INACTIVE => Yii::t('custom_form', 'Inaktív'),
            self::STATUS_ACTIVE => Yii::t('custom_form', 'Aktív'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'custom_questions'], 'required'],
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'custom_questions'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (is_string($this->custom_questions)) {
            $this->custom_questions = explode(',', $this->custom_questions);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if (is_string($this->custom_questions)) {
            $this->custom_questions = explode(',', $this->custom_questions);
        }

        if (empty($this->customQuestions)) {
            $this->customQuestions = $this->getCustomQuestions();
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        $filteredQuestions = [];

        if ($this->custom_questions) {
            foreach (array_unique($this->custom_questions) as $key => $question) {
                if ($question == -1) {
                    continue;
                }

                $filteredQuestions[$key] = $question;
            }
        }

        $this->custom_questions = implode(',', $filteredQuestions);

        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('custom_form', 'ID'),
            'name' => Yii::t('custom_form', 'Név'),
            'description' => Yii::t('custom_form', 'Leírás'),
            'custom_questions' => Yii::t('custom_form', 'Egyedi kérdések'),
            'status' => Yii::t('custom_form', 'Státusz'),
            'created_at' => Yii::t('custom_question', 'Létrehozva'),
            'updated_at' => Yii::t('custom_question', 'Módosítva'),
            'created_by' => Yii::t('custom_question', 'Létrehozta'),
            'updated_by' => Yii::t('custom_question', 'Módosította'),
        ];
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

    /**
     * @param bool $simple
     * @return CustomQuestion[]
     */
    public function getCustomQuestions($simple = false)
    {
        $q = CustomQuestion::find()
            ->where(['status' => CustomQuestion::STATUS_ACTIVE])
            ->andWhere(['id' => $this->custom_questions])
            ->asArray();

        if ($simple) {
            $q->select(
                [
                    'id',
                    'question',
                ]
            );
        }

        $query = $q->all();
        $res = [];

        // Sorting the result by the proper sequence
        if ($this->custom_questions) {
            foreach ($this->custom_questions as $question) {
                foreach ($query as $result) {
                    if ($question == $result['id']) {
                        $res[] = $result;
                    }
                }
            }
        }

        return $res;
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
            function (array $carry, self $customForm) {
                $carry[$customForm->id] = $customForm->name;
                return $carry;
            },
            []
        );
    }
}
