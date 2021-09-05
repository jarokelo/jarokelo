<?php

namespace app\models\db;

use Yii;

/**
 * This is the model class for table "report_taxonomy".
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 *
 * @property CustomForm[] $formRelations
 */
class ReportTaxonomy extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $formRelationList = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_taxonomy';
    }

    /**
     * Compiling a list of the active items
     *
     * @param int $reportId
     * @return array
     */
    public static function getList($reportId = null)
    {
        $q = static::find()
            ->where(
                [
                    'is_active' => self::STATUS_ACTIVE,
                ]
            );

        if ($reportId) {
            $q
                ->leftJoin('report_taxonomy_relation', 'report_taxonomy_relation.report_taxonomy_id=report_taxonomy.id')
                ->andWhere(['report_category_id' => $reportId]);
        }

        $result = $q
            ->orderBy('name')
            ->all();

        return array_reduce(
            $result,
            function (array $carry, self $reportTaxonomy) {
                $carry[$reportTaxonomy->id] = $reportTaxonomy->name;
                return $carry;
            },
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['formRelationList'], 'safe'],
            [['name'], 'required'],
            [['name'], 'unique'],
            ['is_active', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * @return int|null
     */
    public static function getDefaultId()
    {
        /** @var static $entity */
        $entity = static::find()
            ->where(['is_active' => self::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if ($entity) {
            return $entity->id;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $res = true;

        if (!$insert) {
            // Removing previous relations (if there are any)
            CustomFormRelation::deleteAll([
                'type' => CustomFormRelation::TYPE_REPORT_TAXONOMY,
                'entity_id' => $this->id,
            ]);
        }

        $this->formRelationList = array_unique($this->formRelationList);

        if (!empty($this->formRelationList)) {
            // Inserting new relations
            foreach ($this->formRelationList as $key => $formRelationId) {
                if ($formRelationId == -1) {
                    continue;
                }

                $s = (new CustomFormRelation([
                    'type' => CustomFormRelation::TYPE_REPORT_TAXONOMY,
                    'entity_id' => $this->id,
                    'custom_form_id' => $formRelationId,
                    'priority' => $key * 5,
                ]))->save();

                if (!$s) {
                    $res = false;
                }
            }
        }

        if (!$res) {
            throw new \RuntimeException(
                'An error occurred upon saving the form relations'
            );
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormRelations()
    {
        return $this
            ->hasMany(
                CustomFormRelation::class,
                [
                    'entity_id' => 'id',
                ]
            )
            ->where(
                [
                    'type' => CustomFormRelation::TYPE_REPORT_TAXONOMY,
                ]
            );
    }

    /**
     * @return array
     */
    public function getFormRelationList()
    {
        $relations = $this->formRelations;

        if (!$relations) {
            return [];
        }

        return array_reduce(
            $relations,
            function (array $carry, CustomFormRelation $relation) {
                $customForm = $relation->customForm;
                $carry[] = [
                    'priority' => $relation->priority,
                    'id' => $customForm->id,
                ];
                return $carry;
            },
            []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('report_taxonomy', 'ID'),
            'name' => Yii::t('report_taxonomy', 'Név'),
            'is_active' => Yii::t('report_taxonomy', 'Státusz'),
            'formRelationList' => Yii::t('data', 'Kapcsolat létrehozása egyedi űrlapokkal'),
        ];
    }
}
