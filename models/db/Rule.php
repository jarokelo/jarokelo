<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rule".
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $street_group_id
 * @property integer $district_id
 * @property integer $institution_id
 * @property string $report_category_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Report[] $reports
 * @property City $city
 * @property District $district
 * @property Institution $institution
 * @property StreetGroup streetGroup
 * @property RuleContact[] $ruleContacts
 * @property Contact[] $contacts
 */
class Rule extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const WEIGHT_DISTRICT = 1;
    const WEIGHT_CATEGORY = 5;
    const WEIGHT_STREET = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule';
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
            [['city_id', 'institution_id', 'status'], 'required'],
            [['city_id', 'street_group_id', 'district_id', 'institution_id', 'status', 'created_at', 'updated_at', 'report_category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'rule.id'),
            'city_id' => Yii::t('data', 'rule.city_id'),
            'street_group_id' => Yii::t('data', 'rule.street_group_id'),
            'district_id' => Yii::t('data', 'rule.district_id'),
            'institution_id' => Yii::t('data', 'rule.institution_id'),
            'report_category_id' => Yii::t('data', 'rule.report_category_id'),
            'status' => Yii::t('data', 'rule.status'),
            'created_at' => Yii::t('data', 'rule.created_at'),
            'updated_at' => Yii::t('data', 'rule.updated_at'),
        ];
    }

    /**
     * Returns the available statuses for a Rule.
     *
     * @return string[] The available statuses
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('rule', 'status.active'),
            self::STATUS_INACTIVE => Yii::t('rule', 'status.inactive'),
        ];
    }

    /**
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['rule_id' => 'id']);
    }

    /**
     * The City relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * The District relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    /**
     * The Institution relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreetGroup()
    {
        return $this->hasOne(StreetGroup::className(), ['id' => 'street_group_id']);
    }

    /**
     * The RuleContacts relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleContacts()
    {
        return $this->hasMany(RuleContact::className(), ['rule_id' => 'id']);
    }

    /**
     * The Contacts relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['id' => 'contact_id'])
            ->viaTable(RuleContact::tableName(), ['rule_id' => 'id']);
    }

    /**
     * The ReportCategory relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportCategory()
    {
        return $this->hasOne(ReportCategory::className(), ['id' => 'report_category_id']);
    }

    /**
     * Checks, if the Rule matches a Report.
     *
     * @param \app\models\db\Report $report the Report to handle
     *
     * @return bool true, if the Rule can handle the Report
     */
    public function matches($report)
    {
        if (!empty($this->report_category_id) && $this->report_category_id !== $report->report_category_id) {
            return false;
        }

        if ($this->district_id !== null && $this->district_id !== $report->district_id) {
            return false;
        }

        $temp = [];
        if ($this->street_group_id !== null) {
            /** @var \app\models\db\Street[] $streets */
            $streets = $this->streetGroup->getStreets()
                ->where(['name' => $report->street_name])
                ->all();
            return count($streets) > 0;
        }

        return true;
    }

    /**
     * Returns the weight of this Rule.
     *
     * @return int the weight
     */
    public function getWeight()
    {
        if ($this->status == self::STATUS_INACTIVE) {
            return 0;
        }

        return ($this->district_id === null ? 0 : self::WEIGHT_DISTRICT)
        + (empty($this->report_category_id) ? 0 : self::WEIGHT_CATEGORY)
        + ($this->street_group_id === null ? 0 : self::WEIGHT_STREET);
    }

    /**
     * Finds all the matching Rule models for the provided Report ordered by weight.
     *
     * @param \app\models\db\Report $report The provided report
     *
     * @return \app\models\db\Rule[]|null All the matching Rule models ordered by weight
     */
    public static function getMatchingRules($report)
    {
        if ($report === null) {
            return [];
        }

        /* @var \app\models\db\Rule[] $rules */
        $rules = static::find()
            ->where(['city_id' => $report->city_id])
            ->with(['streetGroup'])
            ->all();

        if (empty($rules)) {
            return [];
        }

        $matches = [];

        foreach ($rules as $rule) {
            if ($rule->matches($report)) {
                $matches[$rule->getWeight()] = $rule;
            }
        }

        krsort($matches);

        return $matches;
    }

    /**
     * Finds the best matching Rule for the provided Report.
     *
     * @param \app\models\db\Report $report The provided report
     *
     * @return \app\models\db\Rule|null The best matching Rule
     */
    public static function getMatchingRule($report)
    {
        if ($report === null) {
            return null;
        }

        $matches = static::getMatchingRules($report);

        if (empty($matches)) {
            return null;
        }

        return $matches[max(array_keys($matches))];
    }

    /**
     * Finds the best matching institution id by rules for the provided Report.
     *
     * @param \app\models\db\Report $report The provided report
     *
     * @return int|null The best matching institution id
     */
    public static function getBestMatchingInstituteId($report)
    {
        if ($report === null) {
            return null;
        }

        $match = static::getMatchingRule($report);

        if (empty($match) || empty($match->institution_id)) {
            return null;
        }

        return $match->institution_id;
    }
}
