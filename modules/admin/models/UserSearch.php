<?php

namespace app\modules\admin\models;

use app\models\db\User;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    /**
     * @var string
     */
    public $name_or_email;

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
            ['name_or_email', 'string'],
            ['status', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_or_email' => Yii::t('data', 'user.search.name_or_email'),
            'status'        => Yii::t('data', 'user.search.status'),
        ];
    }

    /**
     * @param array $queryParams
     * @return \yii\data\ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = User::find()
            ->with(['reports']);

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
                        'label' => Yii::t('user', 'index.full_name'),
                        'default' => SORT_ASC,
                    ],
                ],
            ],
        ]);

        if (!($this->load($queryParams) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'OR',
            ['LIKE', 'CONCAT(user.last_name, " ", user.first_name)', $this->name_or_email],
            ['LIKE', 'CONCAT(user.first_name, " ", user.last_name)', $this->name_or_email],
            ['LIKE', 'user.email', $this->name_or_email],
        ])->andFilterWhere([
            'user.status' => $this->status,
        ]);

        return $dataProvider;
    }
}
