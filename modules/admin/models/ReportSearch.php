<?php

namespace app\modules\admin\models;

use app\models\db\AdminCity;
use app\models\db\City;
use app\models\db\Institution;
use app\models\db\Report;
use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Search form for Reports.
 *
 * @package app\modules\admin\models
 */
class ReportSearch extends Model
{
    const SORT_UPDATED_DESC = 0;
    const SORT_UPDATED_ASC  = 1;
    const SORT_CREATED_DESC = 2;
    const SORT_CREATED_ASC  = 3;

    /**
     * @var string
     */
    public $text;

    /**
     * @var boolean
     */
    public $status;

    /**
     * @var int
     */
    public $category;

    /**
     * @var int
     */
    public $city;

    /**
     * @var int
     */
    public $institution;

    /**
     * @var int
     */
    public $highlighted;

    /**
     * @var int
     */
    public $sort;

    /**
     * @var int
     */
    public $user;

    /**
     * @var array
     */
    private $_availableCities = null;

    /**
     * @var array
     */
    private $_availableInstitutions = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['category', 'city', 'institution', 'highlighted', 'user', 'status', 'sort'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'text'        => Yii::t('report', 'search.text'),
            'status'      => Yii::t('report', 'search.status'),
            'city'        => Yii::t('report', 'search.city'),
            'category'    => Yii::t('report', 'search.category'),
            'institution' => Yii::t('report', 'search.institution'),
            'highlighted' => Yii::t('report', 'search.highlighted'),
            'sort'        => Yii::t('report', 'search.sort'),
        ];
    }

    /**
     * Searches the database for Reports.
     *
     * @param array $queryParams The query parameters for filtering
     * @param array $fields Extra values for searching
     * @param integer|null $limit THe maximum number of results to list
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams, $fields = [], $limit = null)
    {
        $query = Report::find()
            ->with(
                [
                    'user',
                    'admin',
                    'district',
                    'institution',
                    'city',
                    'reportAttachments',
                    'reportCategory',
                ]
            )
            ->joinWith(['city', 'reportCategory'])
            ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `report`.`city_id`')
            ->where(['admin_city.admin_id' => Yii::$app->user->id])
            ->andWhere(['not', ['report.status' => [Report::STATUS_DRAFT, Report::STATUS_DELETED]]])
            ->groupBy('report.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
                'attributes' => [
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        if (is_int($limit)) {
            $query->limit($limit);

            $dataProvider->setPagination(false);
        }

        if (!$this->load($queryParams) || !$this->validate()) {
            if (empty($fields)) {
                $this->calcAvailableCities();
                $this->calcAvailableInstitutions(true);

                return $dataProvider;
            }

            $this->text = null;
            $this->status = null;
            $this->category = null;
            $this->city = null;
            $this->institution = null;
            $this->highlighted = null;
        }

        $sortBy = 'updated_at';
        $sortDir = SORT_DESC;

        if ($this->sort !== null) {
            switch ($this->sort) {
                case self::SORT_UPDATED_ASC:
                    $sortDir = SORT_ASC;
                    break;

                case self::SORT_CREATED_ASC:
                    $sortBy = 'created_at';
                    $sortDir = SORT_ASC;
                    break;

                case self::SORT_CREATED_DESC:
                    $sortBy = 'created_at';
                    break;
            }
        }

        $dataProvider->getSort()->defaultOrder = [$sortBy => $sortDir];

        $this->user = isset($fields['user']) ? $fields['user']->id : null;

        if (isset($fields['institution'])) {
            /* @var \app\models\db\Institution $institution */
            $institution = $fields['institution'];

            $this->institution = $institution->id;
            $this->city = $institution->city_id;

            $this->_availableCities = [$institution->city_id => $institution->city->name];
            $this->_availableInstitutions = [$institution->id => $institution->name];
        }

        $uniqueName = Yii::$app->params['report-unique-name'];

        if (!empty($this->text)) {
            $query->andFilterWhere([
                'OR',
                ['LIKE', 'report.name', $this->text],
                ['LIKE', 'report.description', $this->text],
                [
                    'LIKE',
                    "CONCAT(UPPER('{$uniqueName}'), '-', UPPER(`city`.`name`), '-', LPAD(`report`.`id`, 8, '0'))",
                    $this->text,
                ],
            ]);
        }

        $query->andFilterWhere([
            'report.status' => $this->status,
        ])->andFilterWhere([
            'report.report_category_id' => $this->category,
        ])->andFilterWhere([
            'report.user_id' => $this->user,
        ])->andFilterWhere([
            'report.city_id' => $this->city,
        ])->andFilterWhere([
            'report.institution_id' => $this->institution,
        ]);

        if ($this->highlighted) {
            $query = Report::find()->from(['u' => $query->filterHighlighted()])->orderBy(['id' => SORT_DESC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        $this->calcAvailableCities();
        $this->calcAvailableInstitutions(empty($this->city));

        return $dataProvider;
    }

    /**
     * Returns the available Cities.
     *
     * @return string[]
     */
    public function getAvailableCities()
    {
        return $this->_availableCities;
    }

    /**
     * Returns the available Institutions.
     *
     * @return array
     */
    public function getAvailableInstitutions()
    {
        return $this->_availableInstitutions;
    }

    /**
     * Returns the available sorting methods.
     *
     * @return string[]
     */
    public static function getSortData()
    {
        return [
            self::SORT_UPDATED_DESC => Yii::t('report', 'search.sort.updated_desc'),
            self::SORT_UPDATED_ASC  => Yii::t('report', 'search.sort.updated_asc'),
            self::SORT_CREATED_DESC => Yii::t('report', 'search.sort.created_desc'),
            self::SORT_CREATED_ASC  => Yii::t('report', 'search.sort.created_asc'),
        ];
    }

    /**
     * Calculates the Cities available for the current Admin.
     */
    private function calcAvailableCities()
    {
        if ($this->_availableCities !== null) {
            return;
        }

        $this->_availableCities = [];

        /* @var \app\models\db\City $cities */
        $cities = City::find()->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `city`.`id`')->where(['admin_city.admin_id' => Yii::$app->user->id])->all();
        // TODO: limit this search if the 2 field disable (1 - frontend disable, 2 - admin (total) disable) is implemented in the City handling

        foreach ($cities as $city) {
            $this->_availableCities[$city->id] = $city->name;
        }
    }

    /**
     * Calculates the available Institutions for the current City.
     *
     * @param bool $group If true, the Institutions will be grouped by their owning City
     */
    private function calcAvailableInstitutions($group = false)
    {
        if ($this->_availableInstitutions !== null) {
            return;
        }

        if ($this->_availableCities === null) {
            $this->calcAvailableCities();
        }

        $available = ArrayHelper::map(Institution::getInstitutions($this->city, Yii::$app->user->id), 'id', 'name', $group ? 'city_id' : null);

        $this->_availableInstitutions = [];

        if ($group) {
            foreach ($this->_availableCities as $cityId => $name) {
                if (isset($available[$cityId])) {
                    $this->_availableInstitutions[$name] = $available[$cityId];
                }
            }
        } else {
            $this->_availableInstitutions = $available;
        }
    }
}
