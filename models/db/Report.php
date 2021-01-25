<?php

namespace app\models\db;

use app\components\helpers\S3;
use app\components\storage\StorageInterface;
use app\models\ReportMapLayer;
use Yii;
use app\components\Header;
use app\components\helpers\Link;
use app\models\db\query\ReportQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "report".
 *
 * @property integer $id
 * @property integer $city_id
 * @property integer $rule_id
 * @property integer $institution_id
 * @property integer $user_id
 * @property integer $admin_id
 * @property integer $district_id
 * @property string $name
 * @property string $report_category_id
 * @property string $description
 * @property integer $status
 * @property string $user_location
 * @property string $latitude
 * @property string $longitude
 * @property integer $zoom
 * @property integer $sent_email_count
 * @property integer $post_code
 * @property string $street_name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $highlighted
 * @property integer $anonymous
 * @property integer $project
 * @property Email[] $emails
 * @property Notification[] $notifications
 * @property Admin $admin
 * @property City $city
 * @property District $district
 * @property Institution $institution
 * @property ReportCategory $reportCategory
 * @property Rule $rule
 * @property User $user
 * @property ReportActivity[] $reportActivities
 * @property ReportAttachment[] $reportAttachments
 * @property ReportAttachmentOriginal[] $reportAttachmentOriginals
 * @property ReportOriginal $reportOriginal
 * @property ReportMapLayer[] $reportMapLayer
 */
class Report extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_EDITING = 1;
    const STATUS_WAITING_FOR_INFO = 2;
    const STATUS_WAITING_FOR_ANSWER = 3;
    const STATUS_WAITING_FOR_RESPONSE = 4;
    const STATUS_RESOLVED = 5;
    const STATUS_UNRESOLVED = 6;
    const STATUS_DELETED = 7;
    const STATUS_WAITING_FOR_SOLUTION = 8;
    const STATUS_DRAFT = 9;

    const CUSTOM_FILTER_HIGHLIGHTED = 'highlighted';
    const CUSTOM_FILTER_FRESH = 'fresh';
    const CUSTOM_FILTER_NEARBY = 'nearby';
    const CUSTOM_FILTER_FOLLOWED = 'followed';

    const CLOSE_REASON_NOT_ENOUGH_INFO = 'not_enough_info';
    const CLOSE_REASON_NO_ANSWER = 'no_answer';
    const CLOSE_REASON_UNABLE_TO_RESOLVE = 'unable_to_resolve';
    const CLOSE_REASON_NO_RESPONSE = 'no_response';

    const SCENARIO_DRAFT = 'draft';
    const SCENARIO_API = 'api';

    const SOURCE_EDM = 'edm';
    const SOURCE_PDF = 'pdf';
    const SOURCE_EXCEL = 'excel';

    const UNNAMED_ROAD = 'Unnamed Road';

    /**
     * @var int
     */
    const PROJECT_DEFAULT = 0;

    const CATEGORY_POTHOLE = 25;
    const CATEGORY_GREENERY = 26;

    const DONATION_BOX_ACTIVITY_NUMBER = 5;
    const DONATION_BOX_START_DATE = '2020-10-01';

    /**
     * @var array
     */
    public static $projects = [
        self::PROJECT_DEFAULT => 'myProject',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report';
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
            [['name', 'description'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['name', 'description'], 'trim'],
            [['name', 'description'], 'default'],
            [['city_id', 'name'], 'required'],
            [['user_id'], 'required', 'on' => self::SCENARIO_DRAFT],
            [['user_location', 'latitude', 'longitude', 'street_name'], 'required', 'except' => self::SCENARIO_DRAFT],
            [['report_category_id'], 'required', 'except' => [self::SCENARIO_DRAFT]],
            [['city_id', 'rule_id', 'institution_id', 'user_id', 'admin_id', 'district_id', 'status', 'zoom', 'created_at', 'updated_at', 'highlighted', 'anonymous', 'report_category_id', 'post_code'], 'integer'],
            [['description'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'user_location', 'street_name'], 'string', 'max' => 255],
            [['anonymous'], 'in', 'range' => [0, 1]],
            [['project'], 'in', 'range' => array_keys(static::getProjects())],
        ];
    }

    /**
     * @return array
     * @static
     */
    public static function getProjects()
    {
        return [
            self::PROJECT_DEFAULT => Yii::t('data', 'report.project.default'),
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DRAFT] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_API] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'report.id'),
            'city_id' => Yii::t('data', 'report.city_id'),
            'rule_id' => Yii::t('data', 'report.rule_id'),
            'institution_id' => Yii::t('data', 'report.institution_id'),
            'user_id' => Yii::t('data', 'report.user_id'),
            'admin_id' => Yii::t('data', 'report.admin_id'),
            'district_id' => Yii::t('data', 'report.district_id'),
            'name' => Yii::t('data', 'report.name'),
            'report_category_id' => Yii::t('data', 'report.report_category_id'),
            'description' => Yii::t('data', 'report.description'),
            'status' => Yii::t('data', 'report.status'),
            'user_location' => Yii::t('data', 'report.user_location'),
            'latitude' => Yii::t('data', 'report.latitude'),
            'longitude' => Yii::t('data', 'report.longitude'),
            'zoom' => Yii::t('data', 'report.zoom'),
            'created_at' => Yii::t('data', 'report.created_at'),
            'updated_at' => Yii::t('data', 'report.updated_at'),
            'pictures' => Yii::t('data', 'report.pictures'),
            'videos' => Yii::t('data', 'report.videos'),
            'highlighted' => Yii::t('data', 'report.highlighted'),
            'anonymous' => Yii::t('data', 'report.anonymous'),
            'location' => Yii::t('data', 'report.location'),
        ];
    }

    /**
     * Returns the statuses available for a Report.
     *
     * @return string[] The array of available statuses
     */
    public static function statuses()
    {
        return [
            self::STATUS_NEW => Yii::t('const', 'report.status.0'),
            self::STATUS_EDITING => Yii::t('const', 'report.status.1'),
            self::STATUS_WAITING_FOR_INFO => Yii::t('const', 'report.status.2'),
            self::STATUS_WAITING_FOR_ANSWER => Yii::t('const', 'report.status.3'),
            self::STATUS_WAITING_FOR_RESPONSE => Yii::t('const', 'report.status.4'),
            self::STATUS_WAITING_FOR_SOLUTION => Yii::t('const', 'report.status.8'),
            self::STATUS_RESOLVED => Yii::t('const', 'report.status.5'),
            self::STATUS_UNRESOLVED => Yii::t('const', 'report.status.6'),
            self::STATUS_DELETED => Yii::t('const', 'report.status.7'),
            self::STATUS_DRAFT => Yii::t('const', 'report.status.9'),
        ];
    }

    /**
     * @return bool
     */
    public function isDonationBoxAvailable()
    {
        return $this->getReportActivityCount() >= self::DONATION_BOX_ACTIVITY_NUMBER
            && $this->created_at >= strtotime(self::DONATION_BOX_START_DATE);
    }

    /**
     * Returns filtered statuses for admin views
     *
     * @return string[]
     */
    public static function adminFilteredStatuses()
    {
        return array_diff(
            static::statuses(),
            [
                self::STATUS_DELETED => Yii::t('const', 'report.status.7'),
                self::STATUS_DRAFT => Yii::t('const', 'report.status.9'),
            ]
        );
    }

    /**
     * Returns the statuses available for a Report.
     *
     * @return string[] The array of available statuses
     */
    public static function statusFilterItems()
    {
        $customFilters = [
            self::CUSTOM_FILTER_HIGHLIGHTED => Yii::t('const', 'report.filter.popular'),
            self::CUSTOM_FILTER_FRESH => Yii::t('const', 'report.filter.fresh'),
            self::CUSTOM_FILTER_NEARBY => Yii::t('const', 'report.filter.nearby'),
        ];

        $statusFilters = [
            ' ' => [],
            self::STATUS_WAITING_FOR_ANSWER => Yii::t('const', 'report.filter.status.3'),
            self::STATUS_WAITING_FOR_RESPONSE => Yii::t('const', 'report.filter.status.4'),
            self::STATUS_WAITING_FOR_SOLUTION => Yii::t('const', 'report.filter.status.8'),
            self::STATUS_RESOLVED => Yii::t('const', 'report.status.5'),
            self::STATUS_UNRESOLVED => Yii::t('const', 'report.status.6'),
        ];

        $userFilters = Yii::$app->user->isGuest ? [] : [
            self::CUSTOM_FILTER_FOLLOWED => Yii::t('const', 'report.filter.followed'),
        ];

        return ArrayHelper::merge(array_merge($customFilters, $userFilters), $statusFilters);
    }

    /**
     * Returns the publicly available report statuses.
     *
     * @return \string[]
     */
    public static function getPublicStatuses()
    {
        $statuses = static::statuses();
        unset($statuses[self::STATUS_NEW], $statuses[self::STATUS_EDITING], $statuses[self::STATUS_DRAFT], $statuses[self::STATUS_DELETED]);

        return $statuses;
    }

    /**
     * Returns the reasons available for closing a Report.
     *
     * @return array The array of close reasons
     */
    public static function closeReasons()
    {
        return [
            self::CLOSE_REASON_NOT_ENOUGH_INFO => Yii::t('report', 'close.reason.not_enough_info'),
            self::CLOSE_REASON_NO_ANSWER => Yii::t('report', 'close.reason.no_answer'),
            self::CLOSE_REASON_UNABLE_TO_RESOLVE => Yii::t('report', 'close.reason.unable_to_resolve'),
            self::CLOSE_REASON_NO_RESPONSE => Yii::t('report', 'close.reason.no_response'),
        ];
    }

    /**
     * The Emails relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::className(), ['report_id' => 'id']);
    }

    /**
     * The Notifications relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['report_id' => 'id']);
    }

    /**
     * The Admin relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
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
     * The Rule relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * The User relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['report_id' => 'id'])->orderBy([
            'report_activity.created_at' => SORT_DESC,
            'report_activity.id' => SORT_DESC,
        ]);
    }

    /**
     * The ReportAttachments relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachments()
    {
        return $this->hasMany(ReportAttachment::className(), ['report_id' => 'id'])->orderBy(['report_attachment.created_at' => SORT_ASC]);
    }

    /**
     * The ReportAttachmentOriginals relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachmentOriginals()
    {
        return $this->hasMany(ReportAttachmentOriginal::className(), ['report_id' => 'id']);
    }

    /**
     * The ReportMapLayer relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportMapLayer()
    {
        return $this->hasMany(ReportMapLayer::className(), ['report_id' => 'id']);
    }

    /**
     * The ReportOriginal relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportOriginal()
    {
        return $this->hasOne(ReportOriginal::className(), ['report_id' => 'id']);
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
     * The Following users relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFollowers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('report_following', ['report_id' => 'id']);
    }

    /**
     * Returns if the user is following this report
     *
     * @param int $user_id [optional] User id
     *
     * @return bool
     */
    public function isFollowing($user_id = null)
    {
        if (!Yii::$app->user->isGuest) {
            /** @var \app\models\db\User[] $users */
            $users = $this->getFollowers()->all();
            foreach ($users as $user) {
                if ($user->id == ($user_id !== null ? $user_id : Yii::$app->user->id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the unique identifier id for this Report.
     *
     * @return string The unique identifier
     */
    public function getUniqueName()
    {
        $cityName = Inflector::slug($this->city->name);

        return strtoupper(Yii::$app->params['report-unique-name']) . '-' . strtoupper($cityName) . '-' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the unique identifier id for this Report.
     *
     * @return string The unique identifier
     */
    public static function getUniqueNameByIdAndCityName($reportId, $cityName)
    {
        $cityName = Inflector::slug($cityName);

        return strtoupper(Yii::$app->params['report-unique-name']) . '-' . strtoupper($cityName) . '-' . str_pad($reportId, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Returns only the highlighted reports.
     *
     * @param integer $limit [optional] How many entries to return
     * @param array $conditions [optional]
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getHighlighted($limit = null, array $conditions = [])
    {
        $reportIDs = static::find()
            ->select('report.id')
            ->filterAvailable()
            ->andFilterWhere($conditions)
            ->filterHighlighted()
            ->column();

        return static::find()
            ->where(['report.id' => $reportIDs])
            ->orderBy(['report.id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Returns the last submitted reports by a user.
     *
     * @param integer $limit How many entries to return
     * @param array $conditions [optional]
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMyLastReports($limit = null, $conditions = [])
    {
        return static::find()
            ->filterNotDeleted()
            ->andBelongsToActualUser()
            ->andFilterWhere($conditions)
            ->orderBy('created_at DESC')
            ->limit($limit)
            ->all();
    }

    /**
     * Returns the latest reports.
     *
     * @param int $limit [optional]
     * @param array $conditions [optional]
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getLatest($limit = 10, array $conditions = [])
    {
        $query = static::find()
            ->filterAvailable()
            ->orBelongsToActualUser()
            ->andFilterWhere($conditions)
            ->orderBy('created_at DESC');

        return $query->limit($limit)->all();
    }

    /**
     * Returns a list of Reports, which can be filtered by an Admin's permissions.
     * Working with a searching query
     *
     * @param string $q query from param contains an ltrim() to pass zeros during search
     * @param int $page
     * @return \app\models\db\Report[]|void The array of Reports
     */
    public static function getAvailableReports($q, $page)
    {
        if (!$identity = Yii::$app->user->identity) {
            return;
        }

        $whereCondition = ['like', '`report`.`id`', $q];
        $uniqueName = Yii::$app->params['report-unique-name'];

        $query = static::find()
            ->select([
                'report.id',
                'nameAndUniqueId' => new Expression("CONCAT(`report`.`name`, ' (', UPPER('{$uniqueName}'), " .
                    " '-', UPPER(`city`.`name`), '-', LPAD(`report`.`id`, 8, '0'), ')')"),
            ])
            ->where($whereCondition);

        if (!$identity->isSuperAdmin()) {
            $query
                ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
                ->where(['admin_city.admin_id' => $identity->getId()])
                ->andWhere($whereCondition);
        }

        $query->orWhere(['like', '`report`.`name`', $q]);
        $query->leftJoin(City::tableName(), '`city`.`id` = `report`.`city_id`');
        $query->andWhere([
            'report.status' => [
                self::STATUS_WAITING_FOR_ANSWER,
                self::STATUS_WAITING_FOR_RESPONSE,
                self::STATUS_WAITING_FOR_SOLUTION,
                self::STATUS_RESOLVED,
                self::STATUS_UNRESOLVED,
            ],
        ]);

        $limit = 30;
        $offset = ($page - 1) * $limit;
        $query->orderBy(['`report`.`id`' => SORT_DESC])
            ->limit($limit);
        $query->offset($offset);
        $output['results'] = array_values($query->asArray()->all());
        $output['pagination']['more'] = $query->count() > $offset + $limit;

        return $output;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->project = $this->project ?: self::PROJECT_DEFAULT;

        if (!$insert && $this->isAttributeChanged('institution_id', false)) {
            $institutionId = $this->getOldAttribute('institution_id');

            if ($this->institution_id) {
                // initial sending will be skipped
                if ($institutionId) {
                    // institution is changed and it wasn't exist before
                    $this->sent_email_count = 0;
                }
            } else {
                $this->institution_id = $institutionId;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // logging report source (api, web) during insertion
            (new ReportEvent([
                'report_id' => $this->id,
                'source' => $this->getScenario() == self::SCENARIO_API
                    ? ReportEvent::SOURCE_API
                    : ReportEvent::SOURCE_WEB,
            ]))->save();
        }

        if (
            ($insert && $this->status != self::STATUS_DRAFT) ||
            (array_key_exists('status', $changedAttributes) && $this->getOldAttribute('status') == self::STATUS_DRAFT && $this->status != self::STATUS_DRAFT)
        ) {
            $activityOpen = $this->constructActivity(ReportActivity::TYPE_OPEN, [
                'user_id' => $this->user_id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]);
            $activityOpen->detachBehavior('timestamp');
            $activityOpen->save();

            $orig = new ReportOriginal([
                'report_id' => $this->id,
                'name' => $this->name,
                'report_category_id' => $this->report_category_id,
                'description' => $this->description,
                'user_location' => $this->user_location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'zoom' => $this->zoom,
            ]);

            if (!$orig->save()) {
                Yii::error('Unable to insert ReportOriginal! Errors: ' . print_r($orig->getErrors(), true));
            }

            return;
        }

        if (!$insert && array_key_exists('name', $changedAttributes)) {
            $this->addActivity(ReportActivity::TYPE_MOD_NAME, [
                'admin_id' => true,
                'original_value' => $changedAttributes['name'] . '',
                'new_value' => $this->name,
            ]);
        }

        if (!$insert && array_key_exists('report_category_id', $changedAttributes) && !empty($this->report_category_id) && $changedAttributes['report_category_id'] != $this->report_category_id) {
            $this->addActivity(ReportActivity::TYPE_MOD_CATEGORY, [
                'admin_id' => true,
                'original_value' => $changedAttributes['report_category_id'] . '',
                'new_value' => $this->report_category_id,
            ]);
        }

        if (array_key_exists('institution_id', $changedAttributes) && !empty($this->institution_id) && $changedAttributes['institution_id'] != $this->institution_id) {
            $this->addActivity(ReportActivity::TYPE_MOD_INSTITUTION, [
                'admin_id' => true,
                'institution_id' => $this->institution_id,
                'original_value' => $changedAttributes['institution_id'] . '',
                'new_value' => $this->institution_id . '',
            ]);
        }

        if (!$insert && array_key_exists('description', $changedAttributes)) {
            $this->addActivity(ReportActivity::TYPE_MOD_DESCRIPTION, [
                'admin_id' => true,
                'original_value' => $changedAttributes['description'] . '',
                'new_value' => $this->description,
            ]);
        }

        if (!$insert) {
            if (
                array_key_exists('user_location', $changedAttributes) || array_key_exists('latitude', $changedAttributes) ||
                array_key_exists('longitude', $changedAttributes) || (array_key_exists('zoom', $changedAttributes) && intval($this->zoom) !== intval($changedAttributes['zoom']))
            ) {
                $oldUserLocation = array_key_exists('user_location', $changedAttributes) ? $changedAttributes['user_location'] : $this->user_location;
                $oldLatitude = array_key_exists('latitude', $changedAttributes) ? $changedAttributes['latitude'] : $this->latitude;
                $oldLongitude = array_key_exists('longitude', $changedAttributes) ? $changedAttributes['longitude'] : $this->longitude;
                $oldZoom = array_key_exists('zoom', $changedAttributes) ? $changedAttributes['zoom'] : $this->zoom;

                $newLatitude = static::formatCoordinate($this->latitude);
                $newLongitude = static::formatCoordinate($this->longitude);

                $original = Yii::t('report', 'activity.mod_location.format', [
                    'user_location' => $oldUserLocation,
                    'latitude' => $oldLatitude,
                    'longitude' => $oldLongitude,
                    'zoom' => $oldZoom,
                ]);
                $new = Yii::t('report', 'activity.mod_location.format', [
                    'user_location' => $this->user_location,
                    'latitude' => $newLatitude,
                    'longitude' => $newLongitude,
                    'zoom' => $this->zoom,
                ]);

                if ($newLatitude != $oldLatitude || $newLongitude != $oldLongitude) {
                    $this->addActivity(ReportActivity::TYPE_MOD_LOCATION, [
                        'admin_id' => true,
                        'original_value' => $original,
                        'new_value' => $new,
                    ]);
                }
            }
        }
    }

    /**
     * Returns an URL to the first picture of this Report.
     *
     * @param int $size, the Thumbnail will be displayed
     * @return string The URL to the Report's picture
     */
    public function pictureURL($size = ReportAttachment::SIZE_PICTURE_ORIGINAL)
    {
        return static::getPictureUrl($this, $size);
    }

    /**
     * Returns an URL to the specified Report's picture. If the picture is non-existent, a placeholder will be returned.
     *
     * @param \app\models\db\Report|integer|null $report The Report instance or it's id or null
     * @param integer $size, the Thumbnail will be displayed
     * @param bool $useBasePath by default path is generated with a query string parameter,
     * which is model's field value updated_at, to prevent browser cache
     * @return string The URL to the Report's picture
     */
    public static function getPictureUrl(
        $report = null,
        $size = ReportAttachment::SIZE_PICTURE_ORIGINAL,
        $useBasePath = false
    ) {
        $imgFileName = 'default.jpg';
        $updatedAt = null;
        $reportId = null;
        $isImgFileNameSet = false;

        if ($report !== null && is_int($report)) {
            $reportId = $report;
            $reportAttachment = ReportAttachment::getCoverImageByReportId($reportId);
            $isImgFileNameSet = true;

            if ($reportAttachment !== null) {
                $imgRecord = $reportAttachment;
                $updatedAt = $imgRecord->updated_at;
                $imgFileName = $imgRecord->name;
            }
        }

        if (!$isImgFileNameSet && $report !== null && $report instanceof static && count($report->reportAttachments) > 0) {
            /** @var ReportAttachment $imgFile */
            $imgFile = $report->getReportAttachments()
                ->select(['name', 'updated_at', 'storage'])
                ->andWhere(['report_attachment.status' => ReportAttachment::STATUS_VISIBLE])
                ->limit(1)
                ->one();

            if ($imgFile) {
                $imgFileName = $imgFile->getAttribute('name');
                $updatedAt = $imgFile->getAttribute('updated_at');
            }

            $reportId = $report->id;
        }

        $path = 'web/files/report';

        if ($reportId !== null) {
            $path .= '/' . static::fileUrl($reportId);
        }

        $path = implode('/', [$path, ReportAttachment::getPictureFolderBySize($size)]);
        $imgPath = Yii::getAlias("@app/{$path}/{$imgFileName}");
        $value = isset($updatedAt) ? $updatedAt : null;

        if ((isset($imgFile) && $imgFile->isStorageS3()) || (isset($imgRecord) && $imgRecord->isStorageS3())) {
            $s3Image = S3::getPath($path, $imgFileName);

            if ($useBasePath) {
                return $s3Image;
            }

            return sprintf(
                '%s?%s',
                $s3Image,
                $value
            );
        } elseif (file_exists($imgPath) && is_file($imgPath)) {
            // Fallback..
            if ($useBasePath) {
                return Yii::getAlias("@{$path}/{$imgFileName}");
            }

            return sprintf(
                '%s?%s',
                Yii::getAlias("@{$path}/{$imgFileName}"),
                $value
            );
        }

        return static::getPlaceholderImage($size);
    }

    /**
     *
     * Returns the default placeholder image for Reports by size.
     *
     * @param string $size
     * @return bool|string
     */
    public static function getPlaceholderImage($size = ReportAttachment::FOLDER_PICTURE_THUMBNAIL)
    {
        switch ($size) {
            case ReportAttachment::SIZE_PICTURE_EDM:
                $resolution = Yii::$app->params['edm']['width'] . 'x' . Yii::$app->params['edm']['height'];
                break;
            case ReportAttachment::SIZE_PICTURE_MEDIUM:
            case ReportAttachment::SIZE_PICTURE_ORIGINAL:
                $resolution = Yii::$app->params['medium']['width'] . 'x' . Yii::$app->params['medium']['height'];
                break;
            default:
                $resolution = Yii::$app->params['thumb']['width'] . 'x' . Yii::$app->params['thumb']['height'];
                break;
        }

        return Yii::getAlias("@web/images/report/roadblock_{$resolution}.png");
    }

    /**
     * Calculates the folder of the Report for storing files.
     *
     * @return string The folder of the Report
     */
    public function getFileUrl()
    {
        return static::fileUrl($this->id);
    }

    /**
     * Calculates the folder of the Report for storing files.
     *
     * @param integer $reportId The Report's id
     * @return string The folder of the Report
     */
    public static function fileUrl($reportId)
    {
        return floor($reportId / 1000) . "/{$reportId}";
    }

    public function getUrl($source = null)
    {
        if ($this->status === self::STATUS_DRAFT) {
            return Url::to(['/report/create', 'from_id' => $this->id]);
        }

        $city_slug = City::getSlugById($this->city_id);  // This method uses DB cache, don't use "$this->city->slug"
        $urlParts = [Link::REPORTS, $city_slug, $this->id, $this->slug];

        if ($source !== null) {
            switch ($source) {
                case self::SOURCE_PDF:
                    $urlParts[] = Link::POSTFIX_REPORT_PDF;
                    break;
                case self::SOURCE_EDM:
                    $urlParts[] = Link::POSTFIX_REPORT_EDM;
                    break;
                default:
                    break;
            }
        }

        return Link::to($urlParts);
    }

    public function getShortUrl()
    {
        if ($this->status === self::STATUS_DRAFT) {
            return Link::to(Link::CREATE_REPORT, ['from_id' => $this->id]);
        }

        return Link::to([$this->id]);
    }

    public static function getUrlById($id)
    {
        $model = static::findOne($id);

        if ($model === null) {
            return false;
        }

        return $model->getUrl();
    }

    /**
     * Constructs a ReportActivity for this Report.
     *
     * @param string $type the type of the activity
     * @param array $data the activity data
     * @return \app\models\db\ReportActivity
     */
    public function constructActivity($type, $data = [])
    {
        if (isset($data['admin_id']) && $data['admin_id'] === true) {
            if (Yii::$app->controller->module->id === 'admin') {
                $data['admin_id'] = Yii::$app->user->id;
            } else {
                unset($data['admin_id']);
            }
        }

        if (isset($data['user_id']) && $data['user_id'] === true) {
            if (Yii::$app->controller->module->id !== 'admin') {
                $data['user_id'] = Yii::$app->user->id;
            } else {
                unset($data['user_id']);
            }
        }

        return new ReportActivity(ArrayHelper::merge([
            'report_id' => $this->id,
            'type' => $type,
            'is_hidden' => (in_array($this->status, [
                self::STATUS_NEW,
                self::STATUS_EDITING,
            ]) && in_array($type, [
                ReportActivity::TYPE_MOD_INSTITUTION,
                ReportActivity::TYPE_MOD_CATEGORY,
                ReportActivity::TYPE_MOD_DESCRIPTION,
                ReportActivity::TYPE_MOD_LOCATION,
                ReportActivity::TYPE_MOD_NAME,
                ReportActivity::TYPE_MOD_STATUS,
            ])) ? 1 : 0,
        ], $data));
    }

    /**
     * Constructs and inserts a ReportActivity for this Report.
     *
     * @param string $type the type of the activity
     * @param array $data the activity data
     * @return boolean True, if saving the ReportActivity was successful
     * @see Report::createActivity()
     * @deprecated it should return with the added activity itself
     */
    public function addActivity($type, $data = [])
    {
        $activity = $this->constructActivity($type, $data);
        if (!$activity->save()) {
            Yii::error('Unable to insert ReportActivity! Errors: ' . print_r($activity->getErrors(), true));

            return false;
        }

        if (!empty($activity->admin_id) && $this->admin_id != $activity->admin_id) {
            $this->admin_id = $activity->admin_id;
            $this->save();
        }

        return true;
    }

    /**
     * Constructs and inserts a ReportActivity for this Report.
     *
     * @param string $type the type of the activity
     * @param array $data the activity data
     * @return ReportActivity
     */
    public function createActivity($type, $data = [])
    {
        $activity = $this->constructActivity($type, $data);

        if (!$activity->save()) {
            throw new \RuntimeException('Unable to insert ReportActivity');
        }

        if (!empty($activity->admin_id) && $this->admin_id != $activity->admin_id) {
            $this->admin_id = $activity->admin_id;
            $this->save();
        }

        return $activity;
    }

    /**
     * Constructs a ReportAttachment for this Report.
     *
     * @param string $type the type of the attachment
     * @param array $data the attachment data
     * @return \app\models\db\ReportAttachment
     */
    public function constructAttachment($type, $data = [])
    {
        return new ReportAttachment(ArrayHelper::merge([
            'report_id' => $this->id,
            'type' => $type,
            'storage' => StorageInterface::S3,
        ], $data));
    }

    /**
     * Constructs a ReportAttachment for this Report.
     *
     * @param string $type the type of the Attachment
     * @param array $data the attachment data
     * @return boolean True, if saving the ReportAttachment was successful
     */
    public function addAttachment($type, $data = [])
    {
        $attachment = $this->constructAttachment($type, $data);
        if (!$attachment->save()) {
            Yii::error('Unable to insert ReportAttachment! Errors: ' . print_r($attachment->getErrors(), true));

            return false;
        }

        return true;
    }

    /**
     * Soft deletes this Report entry.
     *
     * @throws \Exception
     */
    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        if (!$this->save()) {
            return false;
        }

        return $this->addActivity(ReportActivity::TYPE_DELETE, [
            'admin_id' => true,
        ]);
    }

    /**
     * Resolves the Report and creates a ReportActivity for it.
     *
     * @param string $comment The comment for resolving.
     * @return boolean True, if resolving the Report was successful.
     */
    public function resolve($comment)
    {
        $this->status = self::STATUS_RESOLVED;
        if (!$this->save()) {
            return false;
        }

        return $this->addActivity(ReportActivity::TYPE_RESOLVE, [
            'admin_id' => true,
            'comment' => $comment,
        ]);
    }

    /**
     * Closes the Report and creates a ReportActivity for it.
     *
     * @param string $reason The reason of the closing.
     * @param string $comment The comment for closing.
     * @return boolean True, if closing the Report was successful.
     */
    public function close($reason, $comment)
    {
        $this->status = self::STATUS_UNRESOLVED;
        if (!$this->save()) {
            return false;
        }

        return $this->addActivity(ReportActivity::TYPE_CLOSE, [
            'admin_id' => true,
            'comment' => $comment,
            'new_value' => $reason,
            'is_hidden' => 0,
        ]);
    }

    /**
     * Sets the Report to Waiting for Solution status. After a given time, the Report will automatically turn into Waiting for Response status.
     *
     * @param integer $solutionDate The solution's date timestamp
     * @throws \Exception
     */
    public function waitForSolution($solutionDate)
    {
        $oldStatus = $this->status;

        $this->status = self::STATUS_WAITING_FOR_SOLUTION;

        if ($this->save(true, ['status', 'updated_at'])) {
            $notification = new Notification();
            $notification->user_id = $this->user_id;
            $notification->report_id = $this->id;
            $notification->send_date = $solutionDate;
            $notification->status = Notification::STATUS_WAITING;

            if (!$notification->save()) {
                Yii::error('Unable to insert Notification! Errors: ' . print_r($notification->getErrors(), true));
            }

            $this->addActivity(ReportActivity::TYPE_MOD_STATUS, [
                'admin_id' => true,
                'original_value' => $oldStatus . '',
                'new_value' => $this->status . '',
                'notification_id' => $notification->isNewRecord ? null : $notification->id,
            ]);
        }
    }

    /**
     * Updates the status by the StatusChange modal.
     *
     * @param integer $status The new status
     * @param string|null $comment The comment for the status change
     * @param string|null $reason The reason of closing
     * @param integer|null $solutionDate The solution's date
     */
    public function updateStatus($status, $comment = null, $reason = null, $solutionDate = null)
    {
        if ($status == $this->status) {
            return;
        }

        switch ($status) {
            // Ignored statuses
            case self::STATUS_NEW:
            case self::STATUS_EDITING:
                break;

            case self::STATUS_DELETED:
                $this->softDelete();
                break;

            case self::STATUS_WAITING_FOR_INFO:
            case self::STATUS_WAITING_FOR_ANSWER:
            case self::STATUS_WAITING_FOR_RESPONSE:
                $this->updateStatusInternal($status);
                break;

            case self::STATUS_WAITING_FOR_SOLUTION:
                $this->waitForSolution($solutionDate);
                break;

            case self::STATUS_RESOLVED:
                $this->resolve($comment);
                break;

            case self::STATUS_UNRESOLVED:
                $this->close($reason, $comment);
                break;
        }
    }

    /**
     * Returns the number of resolved reports.
     *
     * @param null $cityId [optional]
     *
     * @return int
     */
    public static function countResolved($cityId = null)
    {
        return Yii::$app->db->cache(function ($db) use ($cityId) {
            return static::find()
                ->filterResolved()
                ->andFilterWhere(['city_id' => $cityId])
                ->count('id');
        }, ArrayHelper::getValue(Yii::$app->params, 'cache.db.report'));
    }

    /**
     * Count all reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserReports($userId)
    {
        return static::find()->where(['user_id' => $userId])->filterNotDeletedOrDaft()->count('id');
    }

    /**
     * Count all the in progress reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserInProgress($userId)
    {
        return static::find()
            ->filterInProgress()
            ->andWhere(['user_id' => $userId])
            ->count('id');
    }

    /**
     * Count all the conditional (decision) reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserWaitingForDecision($userId)
    {
        return static::find()
            ->filterInDecision()
            ->andWhere(['user_id' => $userId])
            ->count('id');
    }

    /**
     * Count all the draft reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserDrafts($userId)
    {
        return static::find()
            ->filterDraft()
            ->andWhere(['user_id' => $userId])
            ->count('id');
    }

    /**
     * Count all the resoluved reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserResolved($userId)
    {
        return static::find()
            ->filterResolved()
            ->andWhere(['user_id' => $userId])
            ->count('id');
    }

    /**
     * Count all the unresolved reports filtered by a user id.
     *
     * @param int $userId
     *
     * @return int|string
     */
    public static function countUserUnresolved($userId)
    {
        return static::find()
            ->filterUnresolved()
            ->andWhere(['user_id' => $userId])
            ->count('id');
    }

    /**
     * Returns the number of unresolved reports.
     *
     * @param null $cityId [optional]
     *
     * @return int
     */
    public static function countUnresolved($cityId = null)
    {
        return Yii::$app->db->cache(function ($db) use ($cityId) {
            return static::find()
                ->filterNotResolved()
                ->andFilterWhere(['city_id' => $cityId])
                ->count('id');
        }, ArrayHelper::getValue(Yii::$app->params, 'cache.db.report'));
    }

    /**
     * Returns the Report by id, if it exists and it is available on the front end.
     *
     * @param integer $id the Report's id
     * @return \app\models\db\Report|null
     */
    public static function findAvailableReport($id = null)
    {
        $query = static::findAvailableReportQuery($id);
        if ($query === null) {
            return null;
        }

        return $query->one();
    }

    /**
     * Returns the Report by id, if it exists and it is available on the front end.
     *
     * @param integer $id the Report's id
     * @return \yii\db\ActiveQuery|null
     */
    public static function findAvailableReportQuery($id = null)
    {
        if ($id === null) {
            return null;
        }

        $query = static::find()->filterAvailable();

        if (!Yii::$app->user->isGuest) {
            $query->orWhere(['user_id' => Yii::$app->user->id]);
        }

        return $query->andWhere(['id' => $id]);
    }

    /**
     * Saves the Report, then creates a ReportActivity for it.
     *
     * @param integer $status The new status
     * @param mixed[] $fields Extra fields for the ReportActivity.
     *
     * @return boolean True, if the Report was saved successfully.
     */
    private function updateStatusInternal($status, $fields = [])
    {
        $oldStatus = $this->status;

        $this->status = $status;

        if ($this->save(true, ['status', 'updated_at'])) {
            return $this->addActivity(ReportActivity::TYPE_MOD_STATUS, ArrayHelper::merge([
                'admin_id' => true,
                'original_value' => $oldStatus . '',
                'new_value' => $this->status . '',
            ], $fields));
        }

        return false;
    }

    /**
     * Sets html meta tags based on the report information.
     *
     * @return void
     */
    public function setTags()
    {
        $tags = [
            Header::TYPE_TITLE => implode(' - ', [$this->name, Yii::t('meta', 'title.default')]),
            Header::TYPE_FB_TITLE => $this->name,
            Header::TYPE_DESCRIPTION => $this->description,
            Header::TYPE_FB_DESCRIPTION => $this->description,
            Header::TYPE_FB_LATITUDE => $this->latitude,
            Header::TYPE_FB_LONGITUDE => $this->longitude,
            Header::TYPE_PLACE_LOCATION_LATITUDE => $this->latitude,
            Header::TYPE_PLACE_LOCATION_LONGITUDE => $this->longitude,
            Header::TYPE_FB_TYPE => Header::FB_TYPE_ARTICLE,
            Header::TYPE_FB_IMAGE => $this->getShareImage(),
            Header::TYPE_FB_ADMINS => '',
        ];

        Header::setAll($tags);
    }

    /**
     * Returns the sharing image of the report.
     *
     * @return string
     */
    public function getShareImage()
    {
        $pictureURL = static::getPictureUrl($this->id, ReportAttachment::SIZE_PICTURE_MEDIUM);

        $image = $pictureURL;

        if (! preg_match('/^http(?s)/', $pictureURL)) {
            $image = Url::base(true) . $pictureURL;
        }

        if ($image == static::getPlaceholderImage()) {
            return Url::to(Header::SHARE_IMAGE_800, true);
        }

        return $image;
    }

    /**
     * Returns the reports filtered by the rss config page.
     *
     * @param array $filters
     *
     * @return \yii\db\ActiveQuery
     */
    public static function filterForRss(array $filters)
    {
        $reports = static::find()->orderBy('created_at DESC');

        /* filter by city */
        if (isset($filters['c']) && strlen(trim($filters['c'])) > 0) {
            $reports->andFilterWhere(['city_id' => $filters['c']]);

            /* if we have a district as well, filter it too */
            if (isset($filters['d']) && strlen(trim($filters['d'])) > 0) {
                $reports->andFilterWhere(['district_id' => $filters['d']]);
            }
        }

        /* filter by institution */
        if (isset($filters['r']) && strlen(trim($filters['r'])) > 0) {
            $reports->andFilterWhere(['institution_id' => $filters['r']]);
        }

        /* filter by category */
        if (isset($filters['t']) && strlen(trim($filters['t'])) > 0) {
            $reports->andFilterWhere(['report_category_id' => $filters['t']]);
        }

        /* filter by status */
        if (isset($filters['s']) && strlen(trim($filters['s'])) > 0) {
            $reports->andFilterWhere(['status' => $filters['s']]);
        }

        /* limit the results */
        if (isset($filters['limit']) && is_numeric($filters['limit'])) {
            $reports->limit($filters['limit']);
        } else {
            $reports->limit(10);
        }

        /* also filter out the reports that are new, draft or deleted */
        $reports->filterAvailable();

        return $reports;
    }

    /**
     * Finds similar reports.
     *
     * @param int $limit
     *
     * @return \yii\db\ActiveQuery
     */
    public function similarReports($limit)
    {
        return static::find()
            ->select([
                'category_match' => new Expression('IF(report_category_id=:category, 1, 0)', [':category' => $this->report_category_id]),
                'institution_match' => new Expression('IF(institution_id=:institution, 1, 0)', [':institution' => $this->institution_id]),
                'city_match' => new Expression('IF(city_id=:city, 1, 0)', [':city' => $this->city_id]),
                'district_match' => new Expression('IF(district_id=:district, 1, 0)', [':district' => $this->district_id]),
                'report.*',
            ])->filterIn2Months()->filterNotThis($this->id)->orderBy([
                'category_match' => SORT_DESC,
                'institution_match' => SORT_DESC,
                'city_match' => SORT_DESC,
                'district_match' => SORT_DESC,
                'created_at' => SORT_DESC,
            ])->filterAvailable()->limit($limit)->all();
    }

    /**
     * @inheritdoc
     * @return ReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReportQuery(get_called_class());
    }

    /**
     * Returns the report statistics for the institutions.
     *
     * @param int $institutionId [optional]
     * @param int $days [optional]
     *
     * @return array
     */
    public static function getStatistics($institutionId = null, $days = 30)
    {
        $daysBefore = strtotime('-' . $days . 'days', strtotime(date('Y-m-d')));

        $q = static::find()->where(['>=', 'created_at', $daysBefore]);

        foreach (static::getPublicStatuses() as $statusId => $status) {
            $statusParam = ':status_id_' . $statusId;
            $statusAlias = 'status' . $statusId;
            $q->addSelect(new Expression("SUM(IF(status={$statusParam}, 1, 0)) AS `{$statusAlias}`", [
                $statusParam => $statusId,
            ]));
        }

        $q->andFilterWhere(['institution_id' => $institutionId]);

        return $q->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }

    /**
     * Returns the attached pictures and videos
     *
     * @param boolean $absolute whether the generated URLs should be absolute.
     * @return array
     */
    public function getPicturesAndVideos($absolute = false)
    {
        $mediaItems = [];
        $pictures = $this->getPictures();
        if ($absolute === true) {
            $baseUrl = Url::base(true);
        } else {
            $baseUrl = '';
        }
        foreach ($pictures as $picture) {
            $mediaItems[] = [
                'image' => $baseUrl . $picture['medium'],
                'url' => $baseUrl . $picture['url'],
                'type' => 'image',
            ];
        }
        $videos = $this->getVideos();
        foreach ($videos as $video) {
            $mediaItems[] = [
                'image' => $video['imageUrl'],
                'url' => $video['videoUrl'],
                'urlFrame' => $video['videoUrlFrame'],
                'type' => 'video',
            ];
        }
        return $mediaItems;
    }

    /**
     * Returns the attached pictures of the report.

     * @param $useBasePath bool
     * @return array
     */
    public function getPictures($useBasePath = false)
    {
        $pictures = [];
        foreach ($this->reportAttachments as $attachment) {
            if ($attachment->type !== ReportAttachment::TYPE_PICTURE || $attachment->status === 0) {
                continue;
            }
            $pictures[] = [
                'id' => $attachment->id,
                'thumbnail' => $attachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_THUMBNAIL, $useBasePath),
                'medium' => $attachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_MEDIUM, $useBasePath),
                'url' => $attachment->getAttachmentUrl(ReportAttachment::SIZE_PICTURE_ORIGINAL, $useBasePath),
            ];
        }
        return $pictures;
    }

    /**
     * Returns the attached videos of the report.
     *
     * @return array
     */
    public function getVideos()
    {
        $videos = [];
        foreach ($this->reportAttachments as $attachment) {
            if ($attachment->type !== ReportAttachment::TYPE_VIDEO || $attachment->status === 0) {
                continue;
            }

            if (!isset($size) || !is_array($size)) {
                $size = [
                    'width' => Yii::$app->params['medium']['width'],
                    'height' => Yii::$app->params['medium']['height'],
                ];
            }

            $data = $attachment->getVideoData();
            if ($data === null || empty($data)) {
                continue;
            }

            $videos[] = [
                'id' => $attachment->id,
                'imageUrl' => $data['imageUrl'],
                'videoUrl' => $data['videoUrl'],
                'videoUrlFrame' => $data['videoUrlFrame'],
            ];
        }
        return $videos;
    }

    /**
     * Returns the comment count. The query is cached.
     *
     * @return int|string
     */
    public function getCommentCount()
    {
        return $this->getDb()->cache(function ($db) {
            return $this->getReportActivities()->where(['type' => [
                ReportActivity::TYPE_COMMENT,
                ReportActivity::TYPE_ANSWER,
            ]])->asArray()->count();
        }, ArrayHelper::getValue(Yii::$app->params, 'cache.db.reportCommentCount'));
    }

    /**
     * @return string
     */
    public function getReportActivityCount()
    {
        return $this->getDb()->cache(function ($db) {
            return $this->getReportActivities()->count();
        }, ArrayHelper::getValue(Yii::$app->params, 'cache.db.reportCommentCount'));
    }

    public function checkUrlIsCorrect($source)
    {
        $actualUrl = Yii::$app->request->getAbsoluteUrl();
        $correctUrl = $this->getUrl($source);

        if ($actualUrl !== $correctUrl) {
            Yii::$app->controller->redirect($correctUrl, 301);
            Yii::$app->end();
        }
    }

    public function isMyReport()
    {
        return Yii::$app->user->id !== null && Yii::$app->user->id == $this->user_id;
    }

    /**
     * @param $user User
     */
    public function toggleFollower($user)
    {
        if ($this->isFollowing($user->id)) {
            $user->unlink('followedReports', $this, true);
        } else {
            $user->link('followedReports', $this);
        }
    }

    public function isCategoryPothole()
    {
        return $this->report_category_id === self::CATEGORY_POTHOLE;
    }

    public static function formatCoordinate($coordinate)
    {
        return round($coordinate, 8);
    }

    /**
     * @return string
     */
    public function getLocationName()
    {
        $location = '';

        if (strpos($this->street_name, self::UNNAMED_ROAD) !== false) { // checking if streetname is Unnamed Road
            // looking for a regular user location (eg Budapest, Veres Péter út 209, 1165 Magyarország) to explode it
            if (preg_match_all('/(,)(.*)(,)/', $this->user_location, $matches)) {
                // exploding location, working with it's middle content (eg Veres Péter út 209)
                if (!empty($matches[2]) && !empty($matches[2][0])) {
                    // trimming and checking location name
                    if (strpos($match = trim($matches[2][0]), self::UNNAMED_ROAD) === false) {
                        // displaying valid location name
                        $location .= $match;
                    } else {
                        // in case of Unnamed Road printing the coordinates (format: lat/lng)
                        $location .= $this->latitude . ' ' . $this->longitude;
                    }
                }
            }
        }

        if (!$location) {
            // in case of location return value is empty, street name is shown
            $location .= $this->street_name;
        }

        // upon district exists it should be displayed always
        $district = District::getDistrictById($this->district_id);
        $location .= $district !== null ? ' (' . $district->name . ')' : '';
        return $location;
    }

    /**
     * returns the visible comments for the current report
     * @return ReportActivity[]
     */
    public function getVisibleComments()
    {
        return ReportActivity::find()
            ->where(['report_id' => $this->id])
            ->andWhere(['is_hidden' => 0])
            ->andWhere(['visible' => 1])
            ->andWhere(
                [
                    'type' => [
                        ReportActivity::TYPE_COMMENT,
                        ReportActivity::TYPE_ANSWER,
                    ],
                ]
            )
            ->orderBy(['id' => SORT_ASC])
            ->with(['admin', 'email', 'institution'])
            ->all();
    }

    /**
     * @return array
     */
    public function getMapLayers()
    {
        if (empty($this->reportMapLayer)) {
            return [];
        }

        $identifiers = [];

        foreach ($this->reportMapLayer as $layer) {
            $identifiers[] = $layer->map_layer_id;
        }

        return $identifiers;
    }

    /**
     * Convert $url to absolute path on disk when the input is not a valid url
     *
     * @param string $url
     * @return string
     */
    public static function preparePictureUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return Yii::getAlias('@app/web' . $url);
    }
}
