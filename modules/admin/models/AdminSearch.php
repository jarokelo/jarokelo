<?php

namespace app\modules\admin\models;

use app\models\db\Admin;


use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search form for Admins.
 *
 * @package app\modules\admin\models
 */
class AdminSearch extends Model
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
     * @var boolean
     */
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_or_email'], 'string'],
            [['city', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_or_email' => Yii::t('data', 'admin.search.name_or_email'),
            'status'        => Yii::t('data', 'admin.search.status'),
            'city'          => Yii::t('data', 'admin.search.city'),
        ];
    }

    /**
     * Searches the database for Admins.
     *
     * @param array $queryParams The query parameters for filtering
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = Admin::find()->groupBy('admin.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'fullName' => SORT_ASC,
                ],
                'attributes' => [
                    'status',
                    'fullName' => [
                        'asc' => ['last_name' => SORT_ASC, 'first_name' => SORT_ASC],
                        'desc' => ['last_name' => SORT_DESC, 'first_name' => SORT_DESC],
                        'label' => Yii::t('admin', 'index.full_name'),
                        'default' => SORT_ASC,
                    ],
                ],
            ],
        ]);

        if (!($this->load($queryParams) && $this->validate())) {
            $query->joinWith('cities');

            return $dataProvider;
        }

        if (Yii::$app->user->identity->status != Admin::STATUS_SUPER_ADMIN && $this->status == Admin::STATUS_SUPER_ADMIN) {
            $this->status = Admin::STATUS_ACTIVE;
        }

        $query->andFilterWhere([
            'OR',
            ['LIKE', 'CONCAT(admin.first_name, " ", admin.last_name)', $this->name_or_email],
            ['LIKE', 'CONCAT(admin.last_name, " ", admin.first_name)', $this->name_or_email],
            ['LIKE', 'admin.email', $this->name_or_email],
        ])
            ->andFilterWhere([
                'admin.status' => $this->status == Admin::STATUS_ACTIVE ? [Admin::STATUS_ACTIVE, Admin::STATUS_SUPER_ADMIN] : $this->status,
            ]);

        if (!empty($this->city)) {
            $query->joinWith('cities')->andFilterWhere(['city.id' => $this->city]);
        } else {
            $query->joinWith('cities');
        }

        return $dataProvider;
    }
}
