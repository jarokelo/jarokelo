<?php

namespace app\modules\admin\models;

use Yii;
use app\models\db\AdminCity;
use app\models\db\City;
use app\models\db\Email;
use app\models\db\ActivityCache;
use yii\base\Model;
use app\models\db\ReportActivity;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * CommentSearch represents the model behind the search form about `app\models\db\ReportActivity`.
 */
class ReportActivityTaskSearch extends ReportActivity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'report_id', 'admin_id', 'user_id', 'institution_id', 'notification_id', 'email_id', 'visible', 'created_at', 'updated_at', 'is_latest', 'is_hidden', 'is_active_task'], 'integer'],
            [['type', 'comment', 'original_value', 'new_value'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @return ArrayDataProvider
     */
    public function search()
    {
        $cityIdQuery = AdminCity::find()
            ->select('city_id')
            ->where(['admin_id' => Yii::$app->user->id])
            ->createCommand()
            ->getRawSql();

        $query = ReportActivity::find()
            ->join('JOIN', Email::tableName(), 'email.id=report_activity.email_id')
            ->join(
                'JOIN',
                City::tableName(),
                [
                    'AND',
                    new Expression('email.to=city.email_address'),
                    new Expression('city.id IN (' . $cityIdQuery . ')'),
                ]
            )
            ->with(['email'])
            ->orderBy(['created_at' => SORT_ASC])
            ->groupBy('report_activity.id');

        // grid filtering conditions
        $query->andFilterWhere(
            [
                'report_activity.is_active_task' => 1,
                'report_activity.type' => [
                    ReportActivity::TYPE_ANSWER,
                    ReportActivity::TYPE_RESPONSE,
                    ReportActivity::TYPE_NO_RESPONSE,
                    ReportActivity::TYPE_NEW_INFO,
                    ReportActivity::TYPE_INCOMING_EMAIL,
                ],
            ]
        );

        // add conditions that should always apply here
        return new ArrayDataProvider(
            [
                'allModels' => array_merge(
                    $query->indexBy('id')->all(),
                    ReportActivity::find()
                        ->where(
                            [
                                'id' => ArrayHelper::getColumn(
                                    ActivityCache::find()
                                        ->select('report_activity_id')
                                        ->where(['admin_id' => Yii::$app->user->getId()])
                                        ->indexBy('report_activity_id')
                                        ->asArray()
                                        ->all(),
                                    'report_activity_id'
                                ),
                            ]
                        )
                        ->indexBy('id')
                        ->all()
                ),
            ]
        );
    }
}
