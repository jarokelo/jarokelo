<?php

namespace app\models\db;

use Yii;

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $last_name
 * @property string $first_name
 * @property string $phone_number
 * @property string $image_file_name
 * @property string $permissions
 * @property integer $status
 * @property string $auth_key
 * @property integer $auth_key_expiration
 * @property string $last_login_at
 * @property string $last_login_ip
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_old_password
 *
 * @property AdminCity[] $adminCities
 * @property AdminPrPage[] $adminPrPages
 * @property City[] $cities
 * @property Report[] $reports
 * @property ReportActivity[] $reportActivities
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SUPER_ADMIN = 2;

    //const PERM_REPORT_VIEV = 0;
    const PERM_REPORT_EDIT = 1;
    //const PERM_REPORT_ADD =  2;
    const PERM_REPORT_DELETE = 3;
    const PERM_ADMIN_VIEW = 4;
    const PERM_ADMIN_EDIT = 5;
    const PERM_ADMIN_ADD = 6;
    const PERM_ADMIN_DELETE = 7;
    const PERM_CITY_VIEW = 8;
    const PERM_CITY_EDIT = 9;
    const PERM_CITY_ADD = 10;
    //const PERM_CITY_DELETE = 11;
    const PERM_USER_VIEW = 12;
    const PERM_USER_EDIT = 13;
    //const PERM_USER_ADD = 14;
    const PERM_USER_DELETE = 15;
    const PERM_INSTITUTION_VIEW = 16;
    const PERM_INSTITUTION_EDIT = 17;
    const PERM_INSTITUTION_ADD = 18;
    const PERM_INSTITUTION_DELETE = 19;
    const PERM_USER_KILL = 20;
    const PERM_USER_FULL_DATA_EXPORT = 21;
    const PERM_PR_PAGE_EDIT = 23;
    const PERM_PR_PAGE_DELETE = 24;
    const PERM_REPORT_STATISTICS = 25;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
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
            [['last_name', 'first_name', 'phone_number'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['last_name', 'first_name', 'phone_number'], 'trim'],
            [['last_name', 'first_name', 'phone_number'], 'default'],
            [['email', 'password_hash', 'last_name', 'first_name'], 'required'],
            [['permissions', 'status', 'last_login_at', 'created_at', 'updated_at', 'is_old_password'], 'integer'],
            [['password_hash', 'last_name', 'first_name', 'phone_number', 'last_login_ip'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('data', 'admin.id'),
            'email' => Yii::t('data', 'admin.email'),
            'password_hash' => Yii::t('data', 'admin.password_hash'),
            'last_name' => Yii::t('data', 'admin.last_name'),
            'first_name' => Yii::t('data', 'admin.first_name'),
            'phone_number' => Yii::t('data', 'admin.phone_number'),
            'image_file_name' => Yii::t('data', 'admin.image_file_name'),
            'permissions' => Yii::t('data', 'admin.permissions'),
            'status' => Yii::t('data', 'admin.status'),
            'last_login_at' => Yii::t('data', 'admin.last_login_at'),
            'last_login_ip' => Yii::t('data', 'admin.last_login_ip'),
            'created_at' => Yii::t('data', 'admin.created_at'),
            'updated_at' => Yii::t('data', 'admin.updated_at'),
        ];
    }

    /**
     * Returns the statuses available to this current logged in Admin.
     *
     * @return string[]
     */
    public static function statuses()
    {
        $statuses = [
            self::STATUS_INACTIVE => Yii::t('admin', 'status.inactive'),
            self::STATUS_ACTIVE   => Yii::t('admin', 'status.active'),
        ];

        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->status == self::STATUS_SUPER_ADMIN) {
            $statuses[self::STATUS_SUPER_ADMIN] = Yii::t('admin', 'status.super_admin');
        }

        return $statuses;
    }

    /**
     * Returns the existing permissions.
     *
     * @return array
     */
    public static function permissions()
    {
        return [
            'admin.permission.report' => [
                //self::PERM_REPORT_VIEW   => Yii::t('const', 'admin.permission.view'),
                self::PERM_REPORT_EDIT       => Yii::t('const', 'admin.permission.edit'),
                //self::PERM_REPORT_ADD    => Yii::t('const', 'admin.permission.add'),
                self::PERM_REPORT_DELETE     => Yii::t('const', 'admin.permission.delete'),
                self::PERM_REPORT_STATISTICS => Yii::t('const', 'admin.permission.statistics'),
            ],
            'admin.permission.admin' => [
                self::PERM_ADMIN_VIEW   => Yii::t('const', 'admin.permission.view'),
                self::PERM_ADMIN_EDIT   => Yii::t('const', 'admin.permission.edit'),
                self::PERM_ADMIN_ADD    => Yii::t('const', 'admin.permission.add'),
                self::PERM_ADMIN_DELETE => Yii::t('const', 'admin.permission.delete'),
            ],
            'admin.permission.city' => [
                self::PERM_CITY_VIEW => Yii::t('const', 'admin.permission.view'),
                self::PERM_CITY_EDIT => Yii::t('const', 'admin.permission.edit'),
                self::PERM_CITY_ADD  => Yii::t('const', 'admin.permission.add'),
                //self::PERM_CITY_DELETE => Yii::t('const', 'admin.permission.delete'),
            ],
            'admin.permission.user' => [
                self::PERM_USER_VIEW   => Yii::t('const', 'admin.permission.view'),
                self::PERM_USER_EDIT   => Yii::t('const', 'admin.permission.edit'),
                //self::PERM_USER_ADD    => Yii::t('const', 'admin.permission.add'),
                self::PERM_USER_DELETE => Yii::t('const', 'admin.permission.delete'),
                self::PERM_USER_KILL   => Yii::t('const', 'admin.permission.kill'),
            ],
            'admin.permission.institution' => [
                self::PERM_INSTITUTION_VIEW   => Yii::t('const', 'admin.permission.view'),
                self::PERM_INSTITUTION_EDIT   => Yii::t('const', 'admin.permission.edit'),
                self::PERM_INSTITUTION_ADD    => Yii::t('const', 'admin.permission.add'),
                self::PERM_INSTITUTION_DELETE => Yii::t('const', 'admin.permission.delete'),
            ],
            'admin.permission.pr_page' => [
                self::PERM_PR_PAGE_EDIT   => Yii::t('const', 'admin.permission.edit'),
                self::PERM_PR_PAGE_DELETE => Yii::t('const', 'admin.permission.delete'),
            ],
        ];
    }

    /**
     * The AdminCities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminCities()
    {
        return $this->hasMany(AdminCity::className(), ['admin_id' => 'id']);
    }

    /**
     * The AdminPrPages relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminPrPages()
    {
        return $this->hasMany(AdminPrPage::className(), ['admin_id' => 'id']);
    }

    /**
     * The AdminPrPages relation.
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getAdminPrPagesAsArray()
    {
        return $this->hasMany(AdminPrPage::className(), ['admin_id' => 'id'])
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }

    /**
     * The Cities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['id' => 'city_id'])->viaTable(AdminCity::tableName(), ['admin_id' => 'id']);
    }

    /**
     * The Reports relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['admin_id' => 'id']);
    }

    /**
     * The ReportActivities relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivities()
    {
        return $this->hasMany(ReportActivity::className(), ['admin_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => [self::STATUS_ACTIVE, self::STATUS_SUPER_ADMIN]]);
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
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
        $this->auth_key_expiration = time() + Yii::$app->params['adminAuthKeyExpiration'];

        $cookie = new Cookie(Yii::$app->user->identityCookie);
        $cookie->value = json_encode([
            $this->id,
            $this->auth_key,
            Yii::$app->params['adminAuthKeyExpiration'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $cookie->expire = $this->auth_key_expiration;

        Yii::$app->response->cookies->add($cookie);
    }

    /**
     * Clears the auth_key and it's expiration time on logout.
     *
     * @return bool True, if the auth key have been successfully cleared.
     */
    public function logout()
    {
        Yii::$app->user->logout();

        $this->auth_key = null;
        $this->auth_key_expiration = null;

        return $this->save();
    }

    /**
     * Returns the full name of the Admin.
     *
     * @return string The concatenated name
     */
    public function getFullName()
    {
        if (Yii::$app->language == 'hu') {
            return $this->last_name . ' ' . $this->first_name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns an URL to the specified Admin's picture. If the picture is non-existent, a placeholder will be returned.
     *
     * @param \app\models\db\Admin|integer|null $admin The Admin instance or it's id or null
     * @param integer $size The size of the picture (for thumbnails)
     * @return string The URL to the Admin's picture
     */
    public static function getPictureUrl($admin = null)
    {
        $imgFileName = 'default.jpg';

        if ($admin !== null) {
            if (is_int($admin)) {
                $admin = static::findOne(['id' => $admin]);
            }

            if ($admin !== null && $admin instanceof static) {
                $imgFileName = $admin->image_file_name;
            }
        }

        $path = '@app/web/files/admin';

        $imgPath = Yii::getAlias("{$path}/{$imgFileName}");
        if (!file_exists($imgPath) || !is_file($imgPath)) {
            $imgPath = Yii::getAlias('@app/web/files/admin/user.png');
            $imgFileName = 'user.png';
        }

        if (!file_exists($imgPath) || !is_file($imgPath)) {
            $imgFileName = 'user.png';
            return Yii::getAlias("@web/images/admin/{$imgFileName}");
        }

        return Yii::getAlias("@web/files/admin/{$imgFileName}");
    }

    /**
     * Returns the comma separated names of the cities , the admin is assigned to.
     *
     * @return string The assigned Cities' names, separated by commas
     */
    public function getAssignedCities()
    {
        $cities = [];

        foreach ($this->cities as $city) {
            $cities[] = $city->name;
        }

        return implode(', ', $cities);
    }

    /**
     * Returns the score of the current Admin.
     *
     * @return integer The score of this Admin
     */
    public function getScore()
    {
        $data = $this->getScoreData();

        return isset($data['total']) ? $data['total'] : 0;
    }

    /**
     * Calculates the score data for the Admin.
     *
     * @return integer[]
     */
    public function getScoreData()
    {
        return static::getDb()->cache(function ($db) {
            return ReportActivity::find()
                ->select([
                    'total'   => new Expression('Sum(IF(type IN (:type1, :type2, :type3, :type4), 1, 0))', [
                        ':type1' => ReportActivity::TYPE_EDITING,
                        ':type2' => ReportActivity::TYPE_SEND_TO_AUTHORITY,
                        ':type3' => ReportActivity::TYPE_RESPONSE,
                        ':type4' => ReportActivity::TYPE_RESOLVE,
                    ]),
                    'editing' => new Expression('SUM(IF(type=:type1, 1, 0))'),
                    'send'    => new Expression('SUM(IF(type=:type2, 1, 0))'),
                    'request' => new Expression('SUM(IF(type=:type3, 1, 0))'),
                    'resolve' => new Expression('SUM(IF(type=:type4, 1, 0))'),
                ])
                ->where(['admin_id' => $this->id])
                ->asArray()
                ->one();
        }, Yii::$app->params['cache']['db']['generalDbQuery']);
    }

    /**
     * Returns the rank of the Admin by ReportActivity.
     *
     * @return int
     */
    public function getRank()
    {
        $userId = $this->id;
        $rankQuery = static::getRankQuery();

        $query = static::find();
        $query->addSelect(['*'])
        ->from(['x' => $rankQuery])
        ->where(['id' => $userId]);
        return $query->asArray()->one()['rank'] ?: Yii::t('const', 'no-information');
    }

    /**
     * @param int $limit
     *
     * @return \yii\db\ActiveQuery
     */
    protected static function getRankQuery($limit = null)
    {
        $points = static::find();

        $points->addSelect(['admin.*', 'points' => new Expression('COUNT(report_activity.id)')])
        ->leftJoin(ReportActivity::tableName(), 'admin.id = report_activity.admin_id')
        ->where([
            'IN',
            'report_activity.type',
            [
                ReportActivity::TYPE_SEND_TO_AUTHORITY,
                ReportActivity::TYPE_RESOLVE,
                ReportActivity::TYPE_RESPONSE,
                ReportActivity::TYPE_EDITING,
            ],
        ])
        ->groupBy(['admin.id'])
        ->orderBy('points DESC');

        $query = static::find();
        $query->addSelect([
            'admin.*',
            new Expression('@prev := @curr'),
            new Expression('@curr := points'),
            'rank' => new Expression('@rank := IF(@prev = @curr, @rank+1, @rank+1)'),
            //'rank' => new Expression('@rank := IF(@prev = @curr, @rank, @rank+1)')
        ])
        ->from(['admin' => '(' . $points->createCommand()->getRawSql() . ')', new Expression('(SELECT @curr := null, @prev := null, @rank := 0) AS sel1')])
        ->orderBy('points DESC, id ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Checks, if the Admin has permission.
     *
     * @param int $permission the permission's id
     * @return bool true, if the Admin has permission
     */
    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return ($this->permissions & (1 << $permission)) != 0;
    }

    /**
     * Checks, the admin has superadmin permission
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        if ($this->status == self::STATUS_SUPER_ADMIN) {
            return true;
        }

        return false;
    }

    /**
     * Sets a permission in the Admin instance.
     *
     * @param int $permission the permission's id
     * @param bool $grant true, if the permission is granted
     */
    public function setPermission($permission, $grant)
    {
        if ($grant) {
            $this->permissions |= (1 << $permission);
        } else {
            $this->permissions &= ~(1 << $permission);
        }
    }

    /**
     * Stores the hashed version of the password into the model.
     *
     * @param string $password the plaintext password
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
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
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

    /**
     * True, if the Admin is Super Admin or has permission to edit the current Pr page
     *
     * @param $id PrPage id
     * @return bool
     */
    public function hasPermissionToPrPage($id)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $query = static::find()->select(['admin_pr_page.pr_page_id'])
        ->where(['<>', 'admin_pr_page.pr_page_id', '""'])
        ->andWhere(['admin_pr_page.admin_id' => $this->id])
        ->leftJoin(AdminPrPage::tableName(), '`admin_pr_page`.`admin_id` = `admin`.`id`');

        $array = [];

        foreach ($query->asArray()->all() as $item) {
            $array[] = $item['pr_page_id'];
        }

        return in_array($id, $array);
    }

    /**
     * Counts the Admin's permissions.
     * @return int
     */
    public function countPermissions()
    {
        return substr_count(strval(decbin($this->permissions)), '1');
    }

    /**
     * Returns true, if the Admin's all permissions exists in the parameter.
     * @param array $permissions
     * @return bool
     */
    public function hasPermissionsOnly($permissions)
    {
        //Starts at 1, because there is always a default permission, with value 2.
        $count = 1;
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                $count++;
            }
        }

        if ($this->countPermissions() != $count) {
            return false;
        }

        return true;
    }
}
