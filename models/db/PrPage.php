<?php
/**
 * Created by PhpStorm.
 * User: laci
 * Date: 2018.05.10.
 * Time: 16:11
 */

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pr_page".
 *
 * @property integer $id
 * @property integer $institution_id
 * @property integer $status
 * @property string $title
 * @property string $info_web_page
 * @property string $info_email
 * @property string $info_phone
 * @property string $info_address
 * @property string $video_url
 * @property string $social_feed_url
 * @property string $custom_color
 * @property string $cover_file_name
 * @property string $logo_file_name
 * @property string $introduction
 * @property string $map_status
 * @property string $slug
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Institution $institution
 */
class PrPage extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_NOT_PUBLIC = 1;
    const STATUS_PUBLIC = 2;

    const MAP_STATUS_ALL = 0;
    const MAP_STATUS_IN_PROGRESS = 1;
    const MAP_STATUS_RESOLVED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pr_page';
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
            [['institution_id', 'title', 'introduction'], 'required'],
            [['status', 'institution_id', 'created_at', 'updated_at'], 'integer'],
            [['slug', 'title', 'info_web_page', 'info_email', 'info_phone', 'info_address', 'video_url', 'social_feed_url', 'custom_color', 'cover_file_name', 'logo_file_name', 'introduction', 'map_status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('pr_page', 'update.title'),
            'introduction' => Yii::t('pr_page', 'update.introduction'),
            'info_web_page' => Yii::t('pr_page', 'update.info.web_page'),
            'info_email' => Yii::t('pr_page', 'update.info.email'),
            'info_phone' => Yii::t('pr_page', 'update.info.phone'),
            'info_address' => Yii::t('pr_page', 'update.info.address'),
            'map_status' => Yii::t('pr_page', 'update.map_status'),
            'custom_color' => Yii::t('pr_page', 'update.custom_color'),
            'status' => Yii::t('pr_page', 'update.status'),
            'social_feed_url' => Yii::t('pr_page', 'update.social_feed_url'),
            'video_url' => Yii::t('pr_page', 'update.video_url'),
            'cover_file_name' => Yii::t('pr_page', 'update.cover_file_name'),
            'logo_file_name' => Yii::t('pr_page', 'update.logo_file_name'),
        ];
    }

    /**
     * Returns the statuses available to this current Pr page.
     *
     * @return string[]
     */
    public static function statuses()
    {
        $statuses = [
            self::STATUS_NOT_PUBLIC => Yii::t('rule', 'status.not_public'),
            self::STATUS_PUBLIC => Yii::t('rule', 'status.public'),
        ];

        return $statuses;
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
     * Returns the available Pr pages list.
     * @return string[] The Pr pages's name in an array, with the Pr page id as key
     */
    public static function availablePrPages()
    {
        $ret = [];

        $query = static::find()->
        filterWhere(['<>', 'status', self::STATUS_INACTIVE]);

        /* @var \app\models\db\PrPage[] $prPages */
        $prPages = $query->all();
        foreach ($prPages as $prPage) {
            $institution = Institution::findOne(['id' => $prPage->institution_id]);
            $ret[$prPage->id] = $institution->name;
        }

        return $ret;
    }

    /**
     * Set the Status to Not Public
     *
     * @return bool True, if the status saved successfully.
     */
    public function activate()
    {
        $this->status = self::STATUS_NOT_PUBLIC;
        return $this->save(false);
    }

    /**
     * Set the Status to Inactive
     *
     * @return bool True, if the status saved successfully.
     */
    public function inactivate()
    {
        $this->status = self::STATUS_INACTIVE;
        return $this->save(false);
    }

    /**
     * Returns the available Map statuses.
     *
     * @return string[] Map statuses
     */
    public static function mapStatuses()
    {
        return [
            self::MAP_STATUS_ALL => Yii::t('pr_page', 'map_status.all'),
            self::MAP_STATUS_IN_PROGRESS => Yii::t('pr_page', 'map_status.in_progress'),
            self::MAP_STATUS_RESOLVED => Yii::t('pr_page', 'map_status.resolved'),
        ];
    }

    /**
     * Returns Status formatted.
     *
     * @return mixed
     */
    public function getStatusFormatted()
    {
        $statusArray = [
            self::STATUS_NOT_PUBLIC => Yii::t('pr_page', 'status.not_public'),
            self::STATUS_PUBLIC => Yii::t('pr_page', 'status.public'),
        ];

        return $statusArray[$this->status];
    }

    /**
     * Returns In Progress or Resolved statuses.
     *
     * @return string[] statuses
     */
    public function getStatusValuesForMapStatus($mapStatus)
    {
        $statusValues = [
            self::MAP_STATUS_IN_PROGRESS => [Report::STATUS_WAITING_FOR_RESPONSE, Report::STATUS_WAITING_FOR_ANSWER, Report::STATUS_WAITING_FOR_SOLUTION],
            self::MAP_STATUS_RESOLVED => [Report::STATUS_RESOLVED],
        ];

        return $statusValues[$mapStatus];
    }

    /**
     * Returns Reports by Map status.
     *
     * @return ActiveDataProvider
     */
    public function reportsOnMap()
    {
        $query = Report::find()
            ->where(['institution_id' => $this->institution_id]);

        if ($this->map_status != self::MAP_STATUS_ALL) {
            $query->andWhere(['IN', 'status', $this->getStatusValuesForMapStatus($this->map_status)]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * Returns an URL to the specified Pr page's cover image.
     *
     * @param \app\models\db\PrPage $model The Pr page instance
     * @return string The URL to the Pr page's cover image
     */
    public static function getCoverUrl($model = null)
    {
        $imgFileName = null;

        if ($model !== null && $model instanceof static) {
            $imgFileName = $model->cover_file_name;
        }

        $path = '@app/web/files/pr-page';

        $imgPath = Yii::getAlias("{$path}/{$imgFileName}");
        if (!file_exists($imgPath) || !is_file($imgPath)) {
            return null;
        }

        return Yii::getAlias("@web/files/pr-page/{$imgFileName}");
    }

    /**
     * Returns an URL to the specified Pr page's logo.
     *
     * @param \app\models\db\PrPage $model The Pr page instance
     * @return string The URL to the Pr page's logo
     */
    public static function getLogoUrl($model = null)
    {
        $imgFileName = null;

        if ($model !== null && $model instanceof static) {
            $imgFileName = $model->logo_file_name;
        }

        $path = '@app/web/files/pr-page';

        $imgPath = Yii::getAlias("{$path}/{$imgFileName}");
        if (!file_exists($imgPath) || !is_file($imgPath)) {
            return null;
        }

        return Yii::getAlias("@web/files/pr-page/{$imgFileName}");
    }


    /**
     * The Pr page news relation.
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getPrPageNews()
    {
        return $this->hasMany(PrPageNews::className(), ['pr_page_id' => 'id'])
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }

    /**
     * The AdminPrPages relation
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function getAdminPrPages()
    {
        return $this->hasMany(AdminPrPage::className(), ['pr_page_id' => 'id'])
            ->createCommand()
            ->cache(ArrayHelper::getValue(Yii::$app->params, 'cache.db.commonStats'))
            ->queryAll();
    }

    /**
     * Searches the database for Pr pages by Admin.
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = static::find()
            ->distinct()
            ->leftJoin(AdminPrPage::tableName(), '`admin_pr_page`.`pr_page_id` = `pr_page`.`id`')
            ->where(['admin_pr_page.admin_id' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ],
                'attributes' => [
                    'title',
                ],
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Counts the Pr pages, which the current admin can edit.
     *
     * @return int
     */
    public function countPrPagesByAdmin()
    {
        $query = static::find()
            ->distinct()
            ->leftJoin(AdminPrPage::tableName(), '`admin_pr_page`.`pr_page_id` = `pr_page`.`id`')
            ->where(['admin_pr_page.admin_id' => Yii::$app->user->id])
            ->count('id');

        return intval($query);
    }
}
