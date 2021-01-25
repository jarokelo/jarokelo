<?php

namespace app\models\db;

use app\components\helpers\CookieAuth;
use Yii;
use app\components\jqueryupload\ImageManipulate;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $password_recovery_token
 * @property string $auth_key
 * @property integer $auth_key_expiration
 * @property string $last_name
 * @property string $first_name
 * @property string $phone_number
 * @property string $image_file_name
 * @property integer $status
 * @property string $last_login_at
 * @property string $last_login_ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property int $activated_at
 * @property integer $city_id
 * @property integer $district_id
 * @property integer $rank
 * @property integer $points
 * @property integer $notification
 * @property integer $notification_owned
 * @property integer $notification_followed
 * @property string $api_token
 * @property string $auth_token
 * @property int $api_rate_limit
 * @property int $api_allowance
 * @property int $api_allowance_updated_at
 * @property int $is_old_password
 * @property int $privacy_policy
 *
 * @property Notification[] $notifications
 * @property Report[] $reports
 * @property ReportActivity[] $reportActivities
 * @property City $city
 * @property District $district
 * @property UserAuth[] $userAuths
 */
class User extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_UNCONFIRMED = 2;

    const PASSWORD_REGEX_LENGTH  = '^(.){6,}$';
    const PASSWORD_REGEX_NUMBER  = '\d{1,}';
    const PASSWORD_REGEX_CAPITAL = '[A-Z]{1,}';

    const NOTIFICATION_DISABLED = 0;
    const NOTIFICATION_IMMEDIATE = 1;
    const NOTIFICATION_DAILY = 2;

    const NOTIFICATION_TYPE_OWNED = 'notification_owned';
    const NOTIFICATION_TYPE_FOLLOWED = 'notification_followed';

    const API_RATE_LIMIT_WINDOW = 1;

    const DELETED_STRING = 'DELETED';
    const DELETED_NUMBER = 0;

    const PRIVACY_POLICY_ACCEPTED = 1;
    const PRIVACY_POLICY_REJECTED = 0;

    /** @var int $rank */
    public $rank;

    /** @var int $points */
    public $points;

    public function init()
    {
        parent::init();
        $this->loadDefaultValues();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
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
            [['district_id'], 'validateDistrict'],
            [['last_name', 'first_name', 'phone_number'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['api_rate_limit', 'email', 'password_hash', 'last_name', 'first_name', 'notification', 'phone_number'], 'trim'],
            [['api_rate_limit', 'email', 'password_hash', 'last_name', 'first_name', 'notification', 'phone_number'], 'default'],
            [['api_rate_limit', 'email', 'last_name', 'first_name', 'notification'], 'required'],
            [['api_rate_limit', 'api_allowance', 'api_allowance_updated_at', 'status', 'last_login_at', 'created_at', 'updated_at', 'activated_at', 'city_id', 'district_id', 'notification', 'notification_owned', 'notification_followed', 'is_old_password'], 'integer'],
            [['email', 'api_token', 'auth_token', 'password_hash', 'password_recovery_token', 'last_name', 'first_name', 'phone_number', 'image_file_name', 'last_login_ip'], 'string', 'max' => 255],
            [['email', 'api_token', 'auth_token'], 'unique'],
            [['email'], 'email'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
        ];
    }

    public function validateDistrict($attribute, $params)
    {
        $validDistricts = District::getAll($this->city_id);

        if (!in_array($this->{$attribute}, array_keys($validDistricts))) {
            $this->district_id = null;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'user.id'),
            'email' => Yii::t('data', 'user.email'),
            'password_hash' => Yii::t('data', 'user.password_hash'),
            'password_recovery_token' => Yii::t('data', 'user.password_recovery_token'),
            'last_name' => Yii::t('data', 'user.last_name'),
            'first_name' => Yii::t('data', 'user.first_name'),
            'phone_number' => Yii::t('data', 'user.phone_number'),
            'image_file_name' => Yii::t('data', 'user.image_file_name'),
            'status' => Yii::t('data', 'user.status'),
            'last_login_at' => Yii::t('data', 'user.last_login_at'),
            'last_login_ip' => Yii::t('data', 'user.last_login_ip'),
            'created_at' => Yii::t('data', 'user.created_at'),
            'updated_at' => Yii::t('data', 'user.updated_at'),
            'city_id' => Yii::t('data', 'user.city_id'),
            'district_id' => Yii::t('data', 'user.district_id'),
            'notification' => Yii::t('data', 'user.notification'),
            'api_token' => Yii::t('data', 'user.api_token'),
            'auth_token' => Yii::t('data', 'user.auth_token'),
            'api_rate_limit' => Yii::t('data', 'user.api_rate_limit'),
            'api_allowance' => Yii::t('data', 'user.api_allowance'),
            'api_allowance_updated_at' => Yii::t('data', 'user.api_allowance_updated_at'),
        ];
    }

    public function getRateLimit($request, $action)
    {
        return [$this->api_rate_limit, self::API_RATE_LIMIT_WINDOW];
    }

    public function loadAllowance($request, $action)
    {
        return [$this->api_allowance, $this->api_allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->api_allowance = $allowance;
        $this->api_allowance_updated_at = $timestamp;
        $this->save();
    }

    /**
     * Returns the available statuses for a User.
     *
     * @return string[] The available statuses
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('user', 'status.active'),
            self::STATUS_INACTIVE => Yii::t('user', 'status.inactive'),
        ];
    }

    /**
     * Returns the notification types the User can subscribe.
     *
     * @return string[] The available notification types the user can subscribe
     */
    public static function notificationTypes()
    {
        return [
            self::NOTIFICATION_TYPE_OWNED => Yii::t('user', 'notificationtype.1'),
            self::NOTIFICATION_TYPE_FOLLOWED => Yii::t('user', 'notificationtype.2'),
        ];
    }

    /**
     * Returns the available notification options for a User.
     *
     * @return string[] The available notification options
     */
    public static function notifications()
    {
        return [
            self::NOTIFICATION_DISABLED => Yii::t('user', 'notification.0'),
            self::NOTIFICATION_IMMEDIATE => Yii::t('user', 'notification.1'),
            self::NOTIFICATION_DAILY => Yii::t('user', 'notification.2'),
        ];
    }

    /**
     * The Notifications relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['user_id' => 'id']);
    }

    /**
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['user_id' => 'id']);
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['user_id' => 'id']);
    }

    /**
     * The UserAuths relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuths()
    {
        return $this->hasMany(UserAuth::className(), ['user_id' => 'id']);
    }

    /**
     * The Followed reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFollowedReports()
    {
        return $this->hasMany(Report::className(), ['id' => 'report_id'])
            ->viaTable('report_following', ['user_id' => 'id']);
    }

    /**
     * The report activity relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportRatings()
    {
        return $this->hasMany(ReportActivity::className(), ['id' => 'activity_id'])
            ->viaTable('report_activity_ratings', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds user by token.
     *
     * @param mixed $token
     * @param null $type
     * @return null|static
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by auth token (API).
     *
     * @param string $token The token
     *
     * @return \app\models\db\User|null The found User or null
     */
    public static function findByAuthToken($token)
    {
        return static::findOne(['auth_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by api token.
     *
     * @param string $token The token
     *
     * @return \app\models\db\User|null The found User or null
     */
    public static function findByApiToken($token)
    {
        return static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email address.
     *
     * @param string $email The email address
     * @param array $status by default fetching active status users
     * @return \app\models\db\User|null The found User or null
     */
    public static function findByEmail($email, $status = [self::STATUS_ACTIVE])
    {
        return static::findOne(['email' => $email, 'status' => $status]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey && time() < $this->auth_key_expiration;
    }

    /**
     * Generates a "remember me" authentication key.
     * @param int $duration
     */
    public function generateAuthKey($duration)
    {
        CookieAuth::removeSession();
        $this->auth_key = Yii::$app->security->generateRandomString();
        $this->auth_key_expiration = time() + $duration;

        $cookie = new Cookie(Yii::$app->user->identityCookie);
        $cookie->value = json_encode([
            $this->id,
            $this->auth_key,
            Yii::$app->params['publicAuthKeyExpiration'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $cookie->expire = $this->auth_key_expiration;

        Yii::$app->response->cookies->add($cookie);
    }

    /**
     * @inheritdoc
     */
    public function setPasswordRecoveryToken()
    {
        $this->password_recovery_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validatePasswordRecoveryToken($token)
    {
        return $token === $this->password_recovery_token;
    }

    /**
     * Returns the full name of the Admin.
     *
     * @return string the concatenated name
     */
    public function getFullName()
    {
        if (Yii::$app->language == 'hu') {
            return $this->last_name . ' ' . $this->first_name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns the number of active users.
     *
     * @return int
     */
    public static function countActive()
    {
        return Yii::$app->db->cache(function ($db) {
            return static::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->count('id');
        }, ArrayHelper::getValue(Yii::$app->params, 'cache.db.user'));
    }

    /**
     * Returns an URL to the specified User's picture. If the picture is non-existent, a placeholder will be returned.
     *
     * @param \app\models\db\User|integer|null $user The User instance or it's id or null
     *
     * @return string The URL to the User's picture
     */
    public static function getPictureUrl($user = null)
    {
        $imgFileName = 'user.png';

        if ($user === null || is_numeric($user)) {
            $user = static::findOne(['id' => $user]);
        }

        if ($user !== null) {
            $imgFileName = $user->image_file_name;
            if (\app\components\helpers\Url::isValidUrl($imgFileName)) {
                return $imgFileName;
            }
        }

        $path = '@app/web/files/user';

        $imgPath = Yii::getAlias("{$path}/{$imgFileName}");

        if (is_file($imgPath)) {
            return Url::to(Yii::getAlias("@web/files/user/{$imgFileName}", true));
        }

        return Url::to(Yii::getAlias('@web/images/user/user.png', true));
    }

    /**
     * Updates the user profile picture if there isn't any explicitly uploaded image
     *
     * @param $url string only valid urls are accepted
     * @return bool whether the saving succeeded
     */
    public function updatePictureUrl($url)
    {
        if (!empty($url) && \app\components\helpers\Url::isValidUrl($url) && (empty($this->image_file_name) || \app\components\helpers\Url::isValidUrl($this->image_file_name)) && $this->image_file_name != $url) {
            $this->image_file_name = $url;
            return $this->save(false, ['image_file_name']);
        }

        return false;
    }

    /**
     * Returns the number of Reports from this User.
     *
     * @return int The report count
     */
    public function getReportCount()
    {
        return count($this->reports);
    }

    /**
     * Stores the hashed version of the password into the model.
     *
     * @param string $password the plaintext password
     *
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function hashPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Rehash the migrated passwords to the new, more secure hash
     *
     * @param $password
     */
    public function hashPasswordIfSha1($password)
    {
        if ($this->password_hash === sha1($password)) {
            $this->hashPassword($password);
            $this->save(false, ['password_hash']);
        }
    }

    /**
     * Checks if the password is hashed.
     *
     * @return bool
     */
    public function isPasswordHashed()
    {
        return substr($this->password_hash, 0, 1) === '$' && substr($this->password_hash, 3, 1) == '$';
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return boolean true, if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Returns the top n users with their ranks based on their reported issues.
     *
     * @param int $limit
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRanks($limit = 10)
    {
        return static::getDb()->cache(function ($db) use ($limit) {
            return static::getRankQuery($limit)->all();
        }, Yii::$app->params['cache']['db']['userRanks']);
    }

    /**
     * Returns the top n users with their ranks based on their reported issues in the current month.
     *
     * @param int $limit
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCurrentMonthRanks($limit = 10)
    {
        return static::getDb()->cache(function ($db) use ($limit) {
            return static::getRankQuery($limit, true)->all();
        }, Yii::$app->params['cache']['db']['userRanks']);
    }

    /**
     * Returns the rank of the User.
     *
     * @return int
     */
    public function getRank($onlyActualMonth = false)
    {
        $rankQuery = static::getRankQuery(10, $onlyActualMonth);

        $rank = (new Query())
            ->select('rank')
            ->from(['toplist' => new Expression("({$rankQuery->createCommand()->getRawSql()})")])
            ->where(['user_id' => $this->id])->createCommand()->cache(Yii::$app->params['cache']['db']['userRanks'])->queryScalar();

        return $rank ?: Yii::t('const', 'no-information');
    }

    /**
     * @param $onlyActualMonth
     * @return ActiveQuery
     */
    public static function getPointCalculatorQuery($onlyActualMonth = false)
    {
        $query = ReportActivity::find()
            ->select([
                'points' => new Expression('Sum(IF(report_activity.type =:type_open OR report_attachment.type=:type_comment_picture, 1, 0))', [
                    ':type_open' => ReportActivity::TYPE_OPEN,
                    ':type_comment_picture' => ReportAttachment::TYPE_COMMENT_PICTURE,
                ]),
            ])
            ->leftJoin(ReportAttachment::tableName(), 'report_attachment.report_activity_id=report_activity.id')
            ->andWhere([
                'report_activity.is_hidden' => 0,
            ])
            ->andWhere('report_activity.user_id IS NOT NULL');

        if ($onlyActualMonth) {
            $firstDay = strtotime(date('Y-m-01'));
            $lastDay = strtotime(date('Y-m-t'));
            $query->andWhere(['>=', 'report_activity.created_at', $firstDay])
                ->andWhere(['<=', 'report_activity.created_at', $lastDay]);
        }

        return $query;
    }

    /**
     * * Returns the points the user collected by submitting reports on the site.
     *
     * @param bool $onlyActualMonth
     * @return false|null|string
     */
    public function getPoints($onlyActualMonth = false)
    {
        $query = static::getPointCalculatorQuery($onlyActualMonth)
            ->andWhere(['report_activity.user_id' => $this->id]);

        if ($onlyActualMonth) {
            $firstDay = strtotime(date('Y-m-01'));
            $lastDay = strtotime(date('Y-m-t'));
            $query->andWhere(['>=', 'report_activity.created_at', $firstDay])
                ->andWhere(['<=', 'report_activity.created_at', $lastDay]);
        }

        return $query->createCommand()->cache(Yii::$app->params['cache']['db']['userRanks'])->queryScalar();
    }

    /**
     * @param integer $limit
     * @param bool $onlyActualMonth [optional]
     *
     * @return \yii\db\ActiveQuery
     */
    protected static function getRankQuery($limit = 10, $onlyActualMonth = false)
    {
        $subQuery = static::getPointCalculatorQuery($onlyActualMonth)->addSelect([
            'user.*',
            'user_id' => 'report_activity.user_id',
        ])
            ->leftJoin(static::tableName(), 'user.id=report_activity.user_id')
            ->andWhere(['NOT LIKE', 'email', '%@myproject.hu', false])
            ->groupBy('report_activity.user_id')
            ->orderBy(['points' => SORT_DESC])
            ->limit($limit);

        if ($onlyActualMonth) {
            $firstDay = strtotime(date('Y-m-01'));
            $lastDay = strtotime(date('Y-m-t'));
            $subQuery->andWhere(['>=', 'report_activity.created_at', $firstDay])
                ->andWhere(['<=', 'report_activity.created_at', $lastDay]);
        }

        return static::find()->addSelect([
            'rank' => new Expression('@rownum := @rownum + 1'),
            'toplist.*',
        ])
            ->from([
                'toplist' => new Expression('(' . $subQuery->createCommand()->getRawSql() . ')'),
                'r' => new Expression('(SELECT @rownum := 0)'),
            ])
            ->where(['>', 'points', 0])
            ->orderBy(['points' => SORT_DESC])
            ->limit($limit);
    }

    /**
     * Collects the User's report data.
     *
     * @return integer[] The data
     */
    public function getReportData()
    {
        return Report::find()
            ->select([
                'total'                => new Expression('Sum(IF(status IN (:status1, :status2, :status3, :status4, :status5, :status6, :status7, :status8), 1, 0))', [
                    ':status1' => Report::STATUS_NEW,
                    ':status2' => Report::STATUS_EDITING,
                    ':status3' => Report::STATUS_WAITING_FOR_ANSWER,
                    ':status4' => Report::STATUS_WAITING_FOR_INFO,
                    ':status5' => Report::STATUS_WAITING_FOR_SOLUTION,
                    ':status6' => Report::STATUS_WAITING_FOR_RESPONSE,
                    ':status7' => Report::STATUS_RESOLVED,
                    ':status8' => Report::STATUS_UNRESOLVED,
                ]),
                'open'                 => new Expression('Sum(IF(status IN (:status1, :status2), 1, 0))'),
                'waiting_for_answer'   => new Expression('SUM(IF(status=:status3, 1, 0))'),
                'waiting_for_info'     => new Expression('SUM(IF(status=:status4, 1, 0))'),
                'waiting_for_solution' => new Expression('SUM(IF(status=:status5, 1, 0))'),
                'waiting_for_response' => new Expression('SUM(IF(status=:status6, 1, 0))'),
                'resolved'             => new Expression('SUM(IF(status=:status7, 1, 0))'),
                'unresolved'           => new Expression('SUM(IF(status=:status8, 1, 0))'),
            ])
            ->where(['user_id' => $this->id])
            ->asArray()
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->status = self::STATUS_INACTIVE;

        return $this->save(false);
    }

    /**
     * Restore (undelete) function
     */
    public function restore()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save(false);
    }

    public function isAddressFilled()
    {
        return !empty($this->city_id);
    }

    public static function getNotificationHint($label)
    {
        switch ($label) {
            case Yii::t('user', 'notification.1'):
                return Yii::t('user', 'notification.1.hint');
                break;
            case Yii::t('user', 'notification.2'):
                return Yii::t('user', 'notification.2.hint');
                break;
            case Yii::t('user', 'notificationtype.1'):
                return Yii::t('user', 'notificationtype.1.hint');
                break;
            case Yii::t('user', 'notificationtype.2'):
                return Yii::t('user', 'notificationtype.2.hint');
                break;
            default:
                return null;
                break;
        }
    }

    public function isNotificationDisabled()
    {
        return $this->notification === self::NOTIFICATION_DISABLED;
    }

    /**
     * Returns the user is allowed the $type notifications
     *
     * @param int $type
     * @return bool
     */
    public function isNotificationAllowed($type = self::NOTIFICATION_DISABLED)
    {
        if ($this->notification == self::NOTIFICATION_DISABLED) {
            return false;
        }

        switch ($type) {
            case self::NOTIFICATION_DAILY:
            case self::NOTIFICATION_IMMEDIATE:
                return $type == $this->notification;
                break;
            case self::NOTIFICATION_TYPE_OWNED:
            case self::NOTIFICATION_TYPE_FOLLOWED:
                return (boolean)$this->{$type};
                break;
            default:
                return false;
                break;
        }
    }

    public static function factory($attributeValues, $password = null)
    {
        $user = new User(array_merge([
            'notification' => self::NOTIFICATION_IMMEDIATE,
            'notification_owned' => 1,
            'notification_followed' => 1,
            'api_rate_limit' => 1,
            'status' => self::STATUS_ACTIVE,
        ], $attributeValues));
        $user->hashPassword($password === null ? uniqid() : $password);

        return $user;
    }

    /**
     * @return $this User
     */
    public function storePicture()
    {
        if (strpos($this->image_file_name, 'upload-tmp') !== false) {
            $fileName = basename($this->image_file_name);
            $destPath = Yii::getAlias('@app/web/files/user/' . $fileName);
            ImageManipulate::crop($this->image_file_name, $destPath, 256, 256);
            $this->image_file_name = $fileName;
        }

        return $this;
    }

    public function hasSocialAuth()
    {
        return count($this->getUserAuths()->all()) > 0;
    }

    /**
     * Don't even think about unkill method...
     *
     * TODO: Error handlings, logging
     */
    public function kill()
    {
        $this->email = self::DELETED_STRING . $hash = Yii::$app->getSecurity()->generatePasswordHash(rand());
        $this->password_hash = self::DELETED_STRING;
        $this->last_name = self::DELETED_STRING;
        $this->first_name = self::DELETED_STRING;
        $this->phone_number = self::DELETED_STRING;
        $this->image_file_name = self::DELETED_STRING;
        $this->status = self::STATUS_INACTIVE;

        $reports = $this->reports;
        foreach ($reports as $report) {
            $reportActivities = $report->reportActivities;
            foreach ($reportActivities as $reportActivity) {
                $reportActivity->comment = self::DELETED_STRING;
                $reportActivity->visible = 0;
                $reportActivity->original_value = self::DELETED_STRING;
                $reportActivity->new_value = self::DELETED_STRING;
                $reportActivity->is_hidden = 1;
                $reportActivity->updateAttributes(['comment', 'visible', 'original_value', 'new_value', 'is_hidden']);
                $reportActivityAttachments = $reportActivity->reportAttachments;
                foreach ($reportActivityAttachments as $reportActivityAttachment) {
                    $reportActivityAttachment->deleteFile();
                }
            }

            $reportOriginal = $report->reportOriginal;
            if ($reportOriginal != null) {
                $reportOriginal->name = self::DELETED_STRING;
                $reportOriginal->description = self::DELETED_STRING;
                $reportOriginal->user_location = 'Budapest';
                $reportOriginal->latitude = self::DELETED_NUMBER;
                $reportOriginal->longitude = self::DELETED_NUMBER;
                $reportOriginal->updateAttributes(['name', 'description', 'user_location', 'latitude', 'longitude']);
            }

            $reportAttachments = $report->reportAttachments;
            foreach ($reportAttachments as $reportAttachment) {
                $reportAttachment->deleteFile();
            }

            $report->name = self::DELETED_STRING;
            $report->description = self::DELETED_STRING;
            $report->status = Report::STATUS_DELETED;
            $report->user_location = 'Budapest';
            $report->latitude = self::DELETED_NUMBER;
            $report->longitude = self::DELETED_NUMBER;
            $report->post_code = self::DELETED_NUMBER;
            $report->street_name = 'Oktogon';
            $report->updateAttributes(['name', 'description', 'status', 'user_location', 'latitude', 'longitude', 'post_code', 'street_name']);
        }

        $reportActivitiesOnOthers = $this->reportActivities;
        foreach ($reportActivitiesOnOthers as $reportActivityOnOther) {
            $reportActivityOnOther->comment = self::DELETED_STRING;
            $reportActivityOnOther->visible = 0;
            $reportActivityOnOther->original_value = self::DELETED_STRING;
            $reportActivityOnOther->new_value = self::DELETED_STRING;
            $reportActivityOnOther->is_hidden = 1;
            $reportActivityOnOther->updateAttributes(['comment', 'visible', 'original_value', 'new_value', 'is_hidden']);
            $reportActivityOnOtherAttachments = $reportActivityOnOther->reportAttachments;
            foreach ($reportActivityOnOtherAttachments as $reportActivityOnOtherAttachment) {
                $reportActivityOnOtherAttachment->deleteFile();
            }
        }

        $userAuths = $this->userAuths;
        foreach ($userAuths as $userAuth) {
            $userAuth->delete();
        }

        return $this->updateAttributes(['status', 'email', 'password_hash', 'last_name', 'first_name', 'phone_number', 'image_file_name', 'status']);
    }

    /**
     * Get all user data for GDPR reasons
     *
     * TODO: Error handlings, logging
     */
    public function fullData()
    {
        $userData = [
            'user_id' => $this->id,
            'email' => $this->email,
            'password_hash' => $this->password_hash,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'phone_number' => $this->phone_number,
            'avatar' => $this->image_file_name,
            'last_login_at' => $this->last_login_at,
            'last_login_ip' => $this->last_login_ip,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'rank' => $this->rank,
            'points' => $this->points,
        ];
        $reportsData = [];
        $reports = $this->reports;
        foreach ($reports as $report) {
            $reportActivitiesData = [];
            $reportActivities = $report->reportActivities;
            foreach ($reportActivities as $reportActivity) {
                if ($reportActivity->user_id === $this->id) {
                    $reportActivitiesData[] = $this->reportActivityToArray($reportActivity);
                }
            }

            $reportOriginalData = [];
            $reportOriginal = $report->reportOriginal;
            if ($reportOriginal != null) {
                $reportOriginalData[] = [
                    'name' => $reportOriginal->name,
                    'description' => $reportOriginal->description,
                    'user_location' => $reportOriginal->user_location,
                    'latitude' => $reportOriginal->latitude,
                    'longitude' => $reportOriginal->longitude,
                    'zoom' => $reportOriginal->zoom,
                    'created_at' => $reportOriginal->created_at,
                    'updated_at' => $reportOriginal->updated_at,
                ];
            }

            $reportAttachmentsData = [];
            $reportAttachments = $report->reportAttachments;
            foreach ($reportAttachments as $reportAttachment) {
                $reportAttachmentsData[] = $this->reportAttachmentToArray($reportAttachment);
            }

            $reportsData[] = [
                'report_id' => $report->id,
                'city' => $report->city->name,
                'report_id' => $report->id,
                'district' => $report->district->name,
                'name' => $report->name,
                'report_category' => $report->reportCategory->name,
                'description' => $report->description,
                'status' => Report::statuses()[$report->status],
                'user_location' => $report->user_location,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'zoom' => $report->zoom,
                'post_code' => $report->post_code,
                'street_name' => $report->street_name,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'anonymous' => $report->anonymous,
                'attachments' => $reportAttachmentsData,
                'original' => $reportOriginalData,
                'activities' => $reportActivitiesData,
            ];
        }

        $reportActivitiesOnOthersData = [];
        $reportActivitiesOnOthers = $this->reportActivities;
        foreach ($reportActivitiesOnOthers as $reportActivityOnOther) {
            $reportActivitiesOnOthersData[] = $this->reportActivityToArray($reportActivityOnOther);
        }

        // TODO: add user, and user auth to user data, reeposrts
        $userAuthsData = [];
        $userAuths = $this->userAuths;
        foreach ($userAuths as $userAuth) {
            $userAuthsData[] = [
                'source' => $userAuth->source,
                'created_at' => $userAuth->created_at,
                'updated_at' => $userAuth->updated_at,
            ];
        }

        return [
            'user' => $userData,
            'reports' => $reportsData,
            'report_activities_on_other_reports' => $reportActivitiesOnOthersData,
            'user_auths' => $userAuthsData,
        ];
    }

    private function reportAttachmentToArray($reportAttachment)
    {
        return [
            'type_id' => $reportAttachment->type,
            'status_id' => $reportAttachment->status,
            'url' => Url::to($reportAttachment->getAttachmentUrl(), true),
            'name' => $reportAttachment->name,
            'created_at' => $reportAttachment->created_at,
            'updated_at' => $reportAttachment->updated_at,
        ];
    }

    private function reportActivityToArray($reportActivity)
    {
        $reportActivityAttachmentsData = [];
        $reportActivityAttachments = $reportActivity->reportAttachments;
        foreach ($reportActivityAttachments as $reportActivityAttachment) {
            $reportActivityAttachmentsData[] = $this->reportAttachmentToArray($reportActivityAttachment);
        }

        return [
            'comment' => $reportActivity->comment,
            'visible' => $reportActivity->visible,
            'original_value' => $reportActivity->original_value,
            'new_value' => $reportActivity->new_value,
            'created_at' => $reportActivity->created_at,
            'updated_at' => $reportActivity->updated_at,
            'attachments' => $reportActivityAttachmentsData,
        ];
    }

    /**
     * @return User|bool
     */
    public function activateRegistration()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->activated_at = time();
        return $this->save() ? $this : false;
    }
}
