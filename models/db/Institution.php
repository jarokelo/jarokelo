<?php

namespace app\models\db;

use Yii;
use app\components\helpers\RomanNumber;
use app\components\helpers\Link;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "institution".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Contact[] $contacts
 * @property City $city
 * @property Report[] $reports
 * @property ReportActivity[] $reportActivities
 * @property Rule[] $rules
 * @property PrPage $prPage
 */
class Institution extends ActiveRecord
{
    const TYPE_TOWN_HALL = 'town_hall';
    const TYPE_DISTRICT = 'district';
    const TYPE_OTHER_INSTITUTION = 'other_institution';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'institution';
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
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'name', 'type'], 'required'],
            [['city_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'type', 'slug'], 'string', 'max' => 255],
            [['note'], 'string'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'institution.id'),
            'city_id' => Yii::t('data', 'institution.city_id'),
            'name' => Yii::t('data', 'institution.name'),
            'type' => Yii::t('data', 'institution.type'),
            'note' => Yii::t('data', 'institution.note'),
            'created_at' => Yii::t('data', 'institution.created_at'),
            'updated_at' => Yii::t('data', 'institution.updated_at'),
        ];
    }

    /**
     * Returns the available Institution types.
     *
     * @return string[] Institution types
     */
    public static function types()
    {
        return [
            self::TYPE_TOWN_HALL => Yii::t('institution', 'type.town_hall'),
            self::TYPE_DISTRICT => Yii::t('institution', 'type.district'),
            self::TYPE_OTHER_INSTITUTION => Yii::t('institution', 'type.other_institution'),
        ];
    }

    /**
     * Returns the institution ID by slug or false
     *
     * @param $slug
     * @return bool|string
     */
    public static function getIdBySlug($slug)
    {
        return static::find()->select('id')->where(['slug' => $slug])->scalar();
    }

    /**
     * Returns the valid statistic url for a city
     * @param int $institutionId
     * @param array $prePath [optional]
     * @return bool|string
     */
    public static function getStatisticUrl($institutionId, $prePath = [])
    {
        $institution = static::findOne($institutionId);

        if ($institution === null) {
            return false;
        }

        return Link::to(array_merge($prePath, [$institution->slug]));
    }

    /**
     * The Contacts relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['institution_id' => 'id']);
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
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['institution_id' => 'id']);
    }

    /**
     * Returns Report count by status.
     *
     * @return integer The count of Reports.
     */
    public function getReportCountByStatus($status)
    {
        if ($status) {
            $results = Report::find()->where([
                'institution_id' => $this->id,
                'status' => $status,
            ])->all();

            return count($results);
        } else {
            return 0;
        }
    }

    /**
     * Returns the resolved Report count by User Id.
     *
     * @return integer The count of Reports.
     */
    public function getResolvedReportCountByUserId($userId)
    {
        if ($userId) {
            $results = Report::find()->where([
                'institution_id' => $this->id,
                'status' => Report::STATUS_RESOLVED,
                'user_id' => $userId,
            ])->all();
            return count($results);
        } else {
            return 0;
        }
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['institution_id' => 'id']);
    }

    /**
     * The Rules relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['institution_id' => 'id']);
    }

    /**
     * Returns the first e-mail address form the contacts.
     *
     * @return string The first found e-mail address
     */
    public function getEmail()
    {
        foreach ($this->contacts as $contact) {
            return $contact->email;
        }

        return '';
    }

    /**
     * Returns the Report count.
     *
     * @return integer The count of Reports.
     */
    public function getReportCount()
    {
        return Report::find()->where(['institution_id' => $this->id])->count();
    }

    /**
     * Extended original method with the ordering district's by roman numbers
     * it sorts them in ascending format in the beginning of array.
     *
     * The result is an associative array.
     *
     * Returns:
     * [
     *    {districtId} => {districtName}
     *    ...
     * ]
     *
     * @param null|int $city The owning City's id
     * @param bool|int $adminId The Admin's id for checking permission
     * @return array
     * @see Institution::getInstitutions()
     */
    public static function getInstitutionsByDistrictRomanValue($city, $adminId)
    {
        $institutions = ArrayHelper::map(static::getInstitutionsQuery($city, $adminId)->all(), 'id', 'name');
        $romanDisctricts = [];

        foreach ($institutions as $key => $value) {
            if ($arabicValue = RomanNumber::romanToInt($value)) {
                // removing districts from original result
                unset($institutions[$key]);
                $romanDisctricts[$value] = [
                    'arabicValue' => $arabicValue,
                    'key' => $key,
                ];
            }
        }

        $districtContainer = [];

        foreach ($romanDisctricts as $key => $row) {
            $districtContainer[$key] = $row['arabicValue'];
        }

        // sorting by arabic value of roman numbers
        array_multisort($districtContainer, SORT_ASC, $romanDisctricts);

        $idContainer = [];

        foreach ($romanDisctricts as $k => $v) {
            $idContainer[$v['key']] = $k;
        }

        // creating final result
        $result = [];

        // all else institution
        foreach ($institutions as $k => $v) {
            $result[$k] = $v;
        }

        // districts
        foreach ($idContainer as $k => $v) {
            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * Returns the list of Institutions, which can be filtered by a City or an Admin's permissions.
     *
     * @param null|int $city The owning City's id
     * @param bool|int $adminId The Admin's id for checking permission
     * @param bool $sortByRomanValue
     * @return Institution[]|array The Institutions available to this City and/or Admin
     */
    public static function getInstitutions($city = null, $adminId = false, $sortByRomanValue = false)
    {
        if ($sortByRomanValue) {
            return static::getInstitutionsByDistrictRomanValue($city, $adminId);
        }

        return static::getInstitutionsQuery($city, $adminId)->all();
    }

    /**
     * Returns the list of Institutions, which can be filtered by a City or an Admin's permissions.
     *
     * @param null|int $city The owning City's id
     * @param bool|int $adminId The Admin's id for checking permission
     * @return Institution[] The Institutions available to this City and/or Admin
     */
    public static function getInstitutionsWeighted($city = null, $adminId = false, $report = null)
    {
        $matchingRules = Rule::getMatchingRules($report);
        $prioInstitutions = [];
        foreach ($matchingRules as $rule) {
            $prioInstitutions[$rule->institution->id] = $rule->institution->name;
        }
        $otherInstitutions = ArrayHelper::map(static::getInstitutionsQuery($city, $adminId)->andFilterWhere([
            'NOT',
            ['institution.id' => array_keys($prioInstitutions)],
        ])->all(), 'id', 'name');

        if (empty($prioInstitutions)) {
            return $otherInstitutions;
        }

        return [
            '' => ['' => Yii::t('app', 'nothing_selected')],
            Yii::t('report', 'update.institution.relevant') => $prioInstitutions,
            Yii::t('report', 'update.institution.other') => $otherInstitutions,
        ];
    }

    /**
     * Returns the list of Institutions, which can be filtered by a City or an Admin's permissions.
     *
     * @param null|int $city The owning City's id
     * @param bool|int $adminId The Admin's id for checking permission
     * @return \yii\db\ActiveQuery The Institutions query
     */
    public static function getInstitutionsQuery($city = null, $adminId = false)
    {
        $query = static::find()->filterWhere(['institution.city_id' => $city])->orderBy(['institution.name' => SORT_ASC]);

        if ($adminId !== false) {
            $query->leftJoin(
                AdminCity::tableName(),
                '`admin_city`.`city_id` = `institution`.`city_id`'
            )->andWhere(['admin_city.admin_id' => $adminId]);
            // TODO: limit this search if the 2 field disable (1 - frontend disable, 2 - admin (total) disable) is implemented in the City handling
        }

        return $query;
    }

    /**
     * Returns the Top n institution statistics.
     *
     * @param int $cityId
     * @param int $limit [optional]
     * @param int $days = [optional]
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getStatistics($cityId, $limit = 10, $days = 30)
    {
        $daysBefore = strtotime('-' . $days . 'days', strtotime(date('Y-m-d')));

        return static::find()
            ->select([
                'id' => 'institution.id',
                'name' => 'institution.name',
                'repCount' => new Expression('SUM(IF(report.status NOT IN (:status0,:status1,:status9,:status7) AND report.created_at > :daysBefore, 1, 0))', [
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
                    ':daysBefore' => $daysBefore,
                ]),
                'inprogress' => new Expression('SUM(IF(report.status IN(:status2,:status3,:status4,:status8) AND report.created_at > :daysBefore, 1, 0))'),
                'resolved' => new Expression('SUM(IF(report.status=:status5 AND report.created_at > :daysBefore, 1, 0))'),
                'unresolved' => new Expression('SUM(IF(report.status=:status6 AND report.created_at > :daysBefore, 1, 0))'),
            ])
            ->leftJoin(Report::tableName(), 'report.institution_id=institution.id')
            ->where(['institution.city_id' => $cityId])
            ->groupBy(['institution.id'])
            ->orderBy(['repCount' => SORT_DESC])
            ->limit($limit)
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }

    /**
     * Returns a list of institutions in the following format:
     * [
     *  [1 => 'institution1'],
     *  [2 => 'institution2'],
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
            ->orderBy('name')
            ->all(), 'id', 'name');
    }

    /**
     * The Pr page relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrPage()
    {
        return $this->hasOne(PrPage::className(), ['institution_id' => 'id']);
    }

    /**
     * Gets all Report activities of the current Institution with send_to_authority and answer types, if the status of report is Resolved.
     * If there is answer for the sent message, then calculates the differences between dates of sending and answering.
     * Finally returns the average.
     *
     * @return int Average days of responses
     * @throws \yii\db\Exception
     */
    public function getResponseDays()
    {
        $sendToActivities = $this->getReportActivities()
            ->select([
                'reportId' => 'report_activity.report_id',
                'institutionId' => 'report_activity.institution_id',
                'createdAt' => 'report_activity.created_at',
            ])
            ->where([
                'report_activity.type' => ReportActivity::TYPE_SEND_TO_AUTHORITY,
                'report.status' => Report::STATUS_RESOLVED,
            ])
            ->leftJoin(Report::tableName(), 'report_activity.report_id = report.id')
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();

        $sumResponseDays = 0;
        $counter = 0;
        foreach ($sendToActivities as $activity) {
            $sendToDate = new \DateTime(date('Y-m-d', $activity['createdAt']));

            $answerActivity = $this->getReportActivities()
                ->select([
                    'createdAt' => 'report_activity.created_at',
                ])
                ->where([
                    'report_activity.type' => ReportActivity::TYPE_ANSWER,
                    'report_activity.report_id' => $activity['reportId'],
                    'report_activity.institution_id' => $activity['institutionId'],
                ])
                ->createCommand()
                ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
                ->queryOne();

            if ($answerActivity) {
                $answerDate = new \DateTime(date('Y-m-d', $answerActivity['createdAt']));
                $interval = date_diff($sendToDate, $answerDate);

                $sumResponseDays += $interval->days;
                $counter++;
            }
        }

        return $counter ? round($sumResponseDays / $counter) : $counter;
    }
}
