<?php

namespace app\modules\admin\models;

use app\models\db\AdminCity;
use app\models\db\Institution;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\db\Report;

/**
 * Search form for Institutions.
 *
 * @package app\modules\admin\models
 */
class InstitutionSearch extends Institution
{
    /**
     * @var string
     */
    public $name_or_email;

    /**
     * @var int
     */
    public $city;

    /**
     * @var int
     */
    public $reportCount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name_or_email', 'string'],
            ['city', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_or_email' => Yii::t('data', 'institution.search.name_or_email'),
            'city'          => Yii::t('data', 'institution.search.city'),
        ];
    }

    /**
     * Searches the database for Institutions.
     *
     * @param array $queryParams The query parameters for filtering
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = static::find()
            ->distinct()
            ->leftJoin(AdminCity::tableName(), '`admin_city`.`city_id` = `institution`.`city_id`')
            ->where(['admin_city.admin_id' => Yii::$app->user->id])
            ->with(['contacts', 'prPage'])
            ->groupBy('institution.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
                'attributes' => [
                    'name',
                ],
            ],
        ]);

        /** @var InstitutionSearch[] $institutions */
        $institutions = array_reduce(
            $dataProvider->getModels(),
            function (array $carry, InstitutionSearch $model) {
                $model->reportCount = 0; // initial value
                $carry[$model->id] = $model;
                return $carry;
            },
            []
        );

        if ($institutions) {
            /** @var array $reports */
            $reportData = Report::find()
                ->where(['institution_id' => array_keys($institutions)])
                ->groupBy('institution_id')
                ->select(['COUNT(*) AS total', 'institution_id'])
                ->indexBy('institution_id')
                ->asArray()
                ->all();

            foreach ($reportData as $institution_id => $model) {
                if (isset($institutions[$institution_id])) {
                    $institutions[$institution_id]->reportCount = $model['total'];
                }
            }
        }

        if (!$this->load($queryParams) || !$this->validate()) {
            $query->with(['contacts']);
            return $dataProvider;
        }

        if (!empty($this->name_or_email)) {
            $query
                ->joinWith('contacts')
                ->andWhere([
                    'OR',
                    ['LIKE', 'institution.name', $this->name_or_email],
                    ['LIKE', 'contact.email', $this->name_or_email],
                ]);
        } else {
            $query->with('contacts');
        }

        if (!empty($this->city)) {
            $query->andWhere(['institution.city_id' => $this->city]);
        }

        return $dataProvider;
    }
}
