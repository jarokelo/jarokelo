<?php

namespace app\modules\admin\models;

use app\models\db\AdminCity;
use app\models\db\City;
use app\models\db\Report;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\db\ReportActivity;
use yii\db\Expression;

/**
 * CommentSearch represents the model behind the search form about `app\models\db\ReportActivity`.
 */
class CommentSearch extends ReportActivity
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $cityIdQuery = AdminCity::find()
            ->select('city_id')
            ->where(['admin_id' => Yii::$app->user->id])
            ->createCommand()
            ->getRawSql();

        $query = ReportActivity::find()
            ->with(['user', 'admin', 'report', 'reportAttachments'])
            ->leftJoin(Report::tableName(), [
                'AND',
                'report_activity.report_id=report.id',
                new Expression('report.city_id IN (' . $cityIdQuery . ')'),
            ])
            ->where([
                'report_activity.is_active_task' => 1,
                'report_activity.type' => ReportActivity::TYPE_COMMENT,
            ])
            ->andWhere(new Expression('report.city_id IN (' . $cityIdQuery . ')'))
            ->orderBy([
                'report_activity.created_at' => SORT_ASC,
                'report_activity.id' => SORT_DESC,
            ])
            ->groupBy('report_activity.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
