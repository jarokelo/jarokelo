<?php

namespace app\models\forms;

use app\components\traits\PrivacyPolicyValidatorTrait;
use app\models\db\District;
use app\models\db\ProjectConfig;
use app\models\db\ReportAttachment;
use app\models\db\ReportTaxonomy;
use Yii;
use app\components\EmailHelper;
use app\models\db\Report;
use app\models\db\User;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\helpers\Link;

class ReportForm extends Report
{
    use PrivacyPolicyValidatorTrait;

    /**
     * @var string[]
     */
    public $pictures;

    /**
     * @var string[]
     */
    public $videos;

    /**
     * @var string
     */
    public $address;

    public $nameFirst;
    public $nameLast;
    public $email;

    public $privacyPolicy;
    public $reportTaxonomyId;
    public $customForm;

    private static $_nameFirst;
    private static $_nameLast;
    private static $_email;

    public function init()
    {
        parent::init();
        $this->setUserAttributes();
        $this->setDefaultLocation();
    }

    private function setUserAttributes()
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $this->user_id = Yii::$app->user->identity->id;
        self::$_nameFirst = $this->nameFirst = Yii::$app->user->identity->first_name;
        self::$_nameLast = $this->nameLast = Yii::$app->user->identity->last_name;
        self::$_email = $this->email = Yii::$app->user->identity->email;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['pictures', 'videos'], 'each', 'rule' => ['string']],
            [['pictures'], 'required', 'on' => self::SCENARIO_DEFAULT, 'message' => Yii::t('report', 'error.missing-pictures')],
            [['address'], 'required', 'except' => self::SCENARIO_DRAFT],
            [['address', 'nameFirst', 'nameLast', 'email'], 'string'],
            [['address', 'nameFirst', 'nameLast', 'email'], 'required'],
            [['email'], 'userValidator', 'when' => function ($model) {
                return $model->user_id === null;
            }],
            [
                ['nameFirst'],
                'compare',
                'compareValue' => self::$_nameFirst,
                'operator' => '===',
                'when' => function ($model) {
                    return !Yii::$app->user->isGuest;
                },
            ],
            [
                ['nameLast'],
                'compare',
                'compareValue' => self::$_nameLast,
                'operator' => '===',
                'when' => function ($model) {
                    return !Yii::$app->user->isGuest;
                },
            ],
            [
                ['email'],
                'compare',
                'compareValue' => self::$_email,
                'operator' => '===',
                'when' => function ($model) {
                    return !Yii::$app->user->isGuest;
                },
            ],
            [
                ['reportTaxonomyId'],
                'required',
                'when' => function ($model) {
                    $isItemAllowed = Yii::$app->db->cache(function () {
                        return ProjectConfig::isItemAllowed(ProjectConfig::KEY_REPORT_TAXONOMIES);
                    }, 60);

                    if ($isItemAllowed) {
                        return true;
                    }

                    return false;
                },
                'message' => Yii::t('report', 'Alkategória nem lehet üres.'),
            ],
            [
                ['reportTaxonomyId'],
                'reportTaxonomyValidator',
            ],
            [['description'], 'required'],
            ['user_location', 'addressValidator'],
            [['privacyPolicy'], 'validatePrivacyPolicy'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'nameFirst' => \Yii::t('report', 'form.name-first'),
            'nameLast' => \Yii::t('report', 'form.name-last'),
            'email' => \Yii::t('report', 'form.email'),
            'acceptTerm' => \Yii::t('report', 'form.email'),
        ]);
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function reportTaxonomyValidator($attribute, array $params = null)
    {
        $taxonomies = Yii::$app->db->cache(function () {
            return ReportTaxonomy::getList($this->report_category_id);
        }, 15);

        if (!in_array($this->{$attribute}, array_keys($taxonomies))) {
            $this->addError($attribute, Yii::t('report', 'Hibás alkategória. Kérjük ellenőrizd a válaszod!'));
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function userValidator($attribute, array $params = null)
    {
        if (($user = User::findOne(['email' => $this->{$attribute}])) === null) {
            return;
        }

        $this->addError($attribute, Yii::t('report', 'error.user-exists-please-log-in', [
            'url' => Link::to(Link::AUTH_LOGIN),
        ]));
    }

    public function addressValidator($attribute, $params)
    {
        $addressFields = ['latitude', 'longitude', 'street_name', 'address'];
        foreach ($addressFields as $addressField) {
            if ($this->hasErrors($addressField)) {
                $this->addError('user_location', Yii::t('report', 'error.something-wrong-with-this-address'));
            }
        }
    }

    /**
     * if the user is guest, then register the new user and set the user_id.
     * if the user is logged in, just set the user_id
     *
     * @throws Exception
     */
    public function setUserId()
    {
        if (!Yii::$app->user->isGuest) {
            $this->user_id = Yii::$app->user->id;
            return;
        }

        $user = User::factory([
            'email' => $this->email,
            'first_name' => $this->nameFirst,
            'last_name' => $this->nameLast,
            'status' => User::STATUS_UNCONFIRMED,
            'privacy_policy' => $this->privacyPolicy,
        ]);

        if (!$user->save()) {
            throw new Exception(Html::errorSummary($user));
        }

        EmailHelper::sendUserRegistrationConfirm($user);
        $this->user_id = $user->id;
    }

    public function setDistrict()
    {
        if (!$this->isAttributeChanged('post_code')) {
            return;
        }

        if ($this->city->has_districts !== 1) {
            return;
        }

        $districtNumber = intval(substr($this->post_code, 1, 2)); // TODO: Work out a way to specify the district from the post_code universally...

        if ($this->district_id === null || $this->district === null || $this->district->number !== $districtNumber) {
            $district = District::findOne(['city_id' => $this->city_id, 'number' => $districtNumber]);
            if ($district === null) {
                return;
            }
            $this->district_id = $district->id;
            unset($this->district);
        }
    }

    public function setPicturesFromDraft()
    {
        foreach ($this->reportAttachments as $reportAttachment) {
            if ($reportAttachment->type == ReportAttachment::TYPE_PICTURE) {
                $this->pictures[] = $reportAttachment->getAttachmentPath(ReportAttachment::SIZE_PICTURE_ORIGINAL);
            }
        }
    }

    private function setDefaultLocation()
    {
        if ($this->city === null) {
            return;
        }

        if ($this->latitude === null) {
            $this->latitude = $this->city->latitude;
        }

        if ($this->longitude === null) {
            $this->longitude = $this->city->longitude;
        }
    }
}
