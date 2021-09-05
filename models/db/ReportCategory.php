<?php

namespace app\models\db;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "report_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 *
 * @property Report[] $reports
 * @property ReportOriginal[] $reportOriginals
 * @property Rule[] $rules
 * @property ReportTaxonomyRelation[] $reportTaxonomyRelations
 * @property CustomForm[] $formRelations
 */
class ReportCategory extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $taxonomyRelationList = [];
    public $formRelationList = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['name'], 'required'],
            [['name'], 'unique'],
            [['is_active'], 'integer'],
            [['taxonomyRelationList', 'formRelationList'], 'safe'],
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

        $result = true;
        $res = true;

        if (!$insert) {
            // Removing previous relations (if there are any)
            ReportTaxonomyRelation::deleteAll([
                'report_category_id' => $this->id,
            ]);
        }

        $this->taxonomyRelationList = array_unique($this->taxonomyRelationList);

        if (!empty($this->taxonomyRelationList)) {
            // Inserting new relations
            foreach ($this->taxonomyRelationList as $key => $relation) {
                if ($relation == -1) {
                    continue;
                }

                $model = (new ReportTaxonomyRelation([
                    'report_category_id' => $this->id,
                    'report_taxonomy_id' => $relation,
                    'priority' => $key * 5,
                ]));
                $save = $model->save();

                if (!$save) {
                    $result = false;
                }
            }
        }

        if (!$result) {
            throw new \RuntimeException(
                'An error occurred upon saving the taxonomy relations'
            );
        }

        if (!$insert) {
            CustomFormRelation::deleteAll([
                'type' => CustomFormRelation::TYPE_REPORT_CATEGORY,
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
                    'type' => CustomFormRelation::TYPE_REPORT_CATEGORY,
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'category.id'),
            'name' => Yii::t('data', 'category.name'),
            'is_active' => Yii::t('data', 'category.is_active'),
            'taxonomyRelationList' => Yii::t('data', 'Kapcsolat létrehozása alkategóriákkal'),
            'formRelationList' => Yii::t('data', 'Kapcsolat létrehozása egyedi űrlapokkal'),
        ];
    }

    /**
     * Composing a list, which includes the identifier of the associated taxonomies
     * @return array
     */
    public function getTaxonomyRelationList()
    {
        $relations = $this->reportTaxonomyRelations;

        if (!$relations) {
            return [];
        }

        $result = array_reduce(
            $relations,
            function (array $carry, ReportTaxonomyRelation $relation) {
                $taxonomy = $relation->reportTaxonomy;
                $carry[] = [
                    'priority' => $relation->priority,
                    'id' => $taxonomy->id,
                    'name' => $taxonomy->name,
                ];
                return $carry;
            },
            []
        );

        uasort($result, function ($a, $b) {
            if (!isset($a['priority']) || !isset($b['priority'])) {
                return;
            }

            // PHP 7 version - $a['priority'] <=> $b['priority]
            return ($a['priority'] < $b['priority'])
                ? -1
                : (($a['priority'] > $b['priority']) ? 1 : 0);
        });

        return array_values($result);
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
     * @return \yii\db\ActiveQuery
     */
    public function getReportTaxonomyRelations()
    {
        return $this->hasMany(ReportTaxonomyRelation::class, ['report_category_id' => 'id']);
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
                    'type' => CustomFormRelation::TYPE_REPORT_CATEGORY,
                ]
            );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['report_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportOriginals()
    {
        return $this->hasMany(ReportOriginal::className(), ['report_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['report_category_id' => 'id']);
    }

    /**
     * Returns a list of report categories in the following format:
     * [
     *  [1 => 'category1'],
     *  [2 => 'category2'],
     *  ...
     * ]
     *
     * Ideal for use as drop down list data source for example.
     *
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(static::find()
            ->where(['is_active' => 1])
            ->orderBy('name')
            ->all(), 'id', 'name');
    }

    /**
     * Returns the Report Category statistics.
     *
     * @param int $cityId [optional]
     * @param int $institutionId [optional]
     * @param int $limit [optional]
     *
     * @return array
     */
    public static function getStatistics($cityId = null, $institutionId = null, $limit = 10)
    {
        return static::find()
            ->select([
                'id' => 'report_category.id',
                'name' => 'report_category.name',
                'repCount' => new Expression('SUM(IF(report.status NOT IN (:status0,:status1,:status9,:status7), 1, 0))', [
                    ':status0' => Report::STATUS_NEW,
                    ':status1' => Report::STATUS_EDITING,
                    ':status2' => Report::STATUS_WAITING_FOR_INFO,
                    ':status3' => Report::STATUS_WAITING_FOR_ANSWER,
                    ':status4' => Report::STATUS_WAITING_FOR_RESPONSE,
                    ':status5' => Report::STATUS_RESOLVED,
                    ':status6' => Report::STATUS_UNRESOLVED,
                    ':status7' => Report::STATUS_DELETED,
                    ':status8' => Report::STATUS_WAITING_FOR_SOLUTION,
                    ':status9' => Report::STATUS_DRAFT,
                ]),
                'inprogress' => new Expression('SUM(IF(report.status IN(:status2,:status3,:status4,:status8), 1, 0))'),
                'resolved' => new Expression('SUM(IF(report.status=:status5, 1, 0))'),
                'unresolved' => new Expression('SUM(IF(report.status=:status6, 1, 0))'),
            ])
            ->leftJoin(Report::tableName(), 'report.report_category_id=report_category.id')
            ->andFilterWhere(['report.city_id' => $cityId])
            ->andFilterWhere(['report.institution_id' => $institutionId])
            ->groupBy(['report_category.id'])
            ->orderBy(['repCount' => SORT_DESC])
            ->limit($limit)
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }
}
