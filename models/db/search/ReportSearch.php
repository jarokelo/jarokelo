<?php

namespace app\models\db\search;

use app\models\db\query\ReportQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\db\Report;
use yii\db\Query;

/**
 * ReportSearch represents the model behind the search form about `app\models\db\Report`.
 */
class ReportSearch extends Report
{
    public $city_name;
    public $location;
    public $date_from;
    public $date_to;
    public $waiting_for_answer;
    public $resolved;
    public $waiting_for_solution;
    public $waiting_for_response;
    public $unresolved;
    public $followed;
    public $address;
    public $users_reports;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'rule_id', 'institution_id', 'user_id', 'admin_id', 'district_id', 'zoom', 'created_at', 'updated_at', 'sent_email_count', 'highlighted', 'report_category_id'], 'integer'],
            [['address', 'date_from', 'date_to', 'location', 'city_name', 'name', 'report_category_id', 'institution_id', 'description', 'user_location', 'post_code', 'street_name', 'status'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['users_reports', 'waiting_for_answer', 'resolved', 'waiting_for_solution', 'waiting_for_response', 'unresolved', 'followed'], 'boolean'],
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
     * @param null $customFilter [optional]
     * @param float $lat [optional]
     * @param float $long [optional]
     *
     * @return ActiveDataProvider
     */
    public function search($params, $customFilter = null, $lat = null, $long = null)
    {
        $query = Report::find()
            ->filterAvailable()
            ->orderBy(['report.id' => SORT_DESC]);

        // not highlighted
        if ($query->union === null && $customFilter !== self::CUSTOM_FILTER_FOLLOWED) {
            $query->orBelongsToActualUser();
        }

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 6,
                'totalCount' => $query->count(),
            ],
        ]);

        $this->load($params);

        if (!in_array($params['ReportSearch']['status'], [
            Report::CUSTOM_FILTER_HIGHLIGHTED,
            Report::CUSTOM_FILTER_NEARBY,
            Report::CUSTOM_FILTER_FRESH,
            Report::CUSTOM_FILTER_FOLLOWED,
        ])) {
            $query->andFilterWhere([
                'report.status' => $this->status,
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'report.id' => $this->id,
            'report.city_id' => $this->city_id,
            'report.rule_id' => $this->rule_id,
            'report.institution_id' => $this->institution_id,
            'report.user_id' => $this->user_id,
            'report.admin_id' => $this->admin_id,
            'report.district_id' => $this->district_id,
            'report.latitude' => $this->latitude,
            'report.longitude' => $this->longitude,
            'report.zoom' => $this->zoom,
            'report.created_at' => $this->created_at,
            'report.updated_at' => $this->updated_at,
            'report.sent_email_count' => $this->sent_email_count,
            'report.highlighted' => $this->highlighted,
            'report.report_category_id' => $this->report_category_id,
        ]);

        $query->andFilterWhere([
            'OR',
            ['like', 'report.name', $this->name],
            ['like', 'report.description', $this->name],
            ['like', 'report.user_location', $this->name],
            ['like', 'report.street_name', $this->name],
        ]);

        if (array_key_exists('date_from', $params['ReportSearch']) && $params['ReportSearch']['date_from'] != 0) {
            $dateFrom = date('Y-m-d H:i:s', strtotime($params['ReportSearch']['date_from'] . ' 00:00:00'));
            $query->andWhere('report.created_at >= :date_from', [':date_from' => strtotime($dateFrom)]);

            if (!(array_key_exists('date_to', $params['ReportSearch']) && $params['ReportSearch']['date_to'] != 0)) {
                $date = date_create_from_format('Y-m-d', $params['ReportSearch']['date_from'])->modify('+30 day');
                $dateTo = date('Y-m-d H:i:s', strtotime(date_format($date, 'Y-m-d') . ' 23:59:59'));
                $query->andWhere(':date_to >= report.created_at', [
                    ':date_to' => strtotime($dateTo),
                ]);
            }
        }
        if (array_key_exists('date_to', $params['ReportSearch']) && $params['ReportSearch']['date_to'] != 0) {
            $dateTo = date('Y-m-d H:i:s', strtotime($params['ReportSearch']['date_to'] . ' 23:59:59'));
            $query->andWhere(':date_to >= report.created_at', [':date_to' => strtotime($dateTo)]);

            if (!(array_key_exists('date_from', $params['ReportSearch']) && $params['ReportSearch']['date_from'] != 0)) {
                $date = date_create_from_format('Y-m-d', $params['ReportSearch']['date_to'])->modify('-30 day');
                $dateFrom = date('Y-m-d H:i:s', strtotime(date_format($date, 'Y-m-d') . ' 00:00:00'));
                $query->andWhere('report.created_at >= :date_from', [
                    ':date_from' => strtotime($dateFrom),
                ]);
            }
        }

        $statusArray = [];

        if (array_key_exists('lat', $params['ReportSearch']) && array_key_exists('lng', $params['ReportSearch']) && $params['ReportSearch']['lat'] !== null && $params['ReportSearch']['lng'] !== null) {
            $distance = 0.5;
            $radius = 6371;
            $lat = $params['ReportSearch']['lat'];
            $lng = $params['ReportSearch']['lng'];

            $maxlng = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
            $minlng = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));

            $maxlat = $lat + rad2deg($distance / $radius);
            $minlat = $lat - rad2deg($distance / $radius);

            $query->andWhere('report.latitude BETWEEN :minLat AND :maxLat', [
                ':minLat' => $minlat,
                ':maxLat' => $maxlat,
            ])
                ->andWhere('report.longitude BETWEEN :minLng AND :maxLng', [
                    ':minLng' => $minlng,
                    ':maxLng' => $maxlng,
                ]);
        }

        if (array_key_exists('followed', $params['ReportSearch']) && $params['ReportSearch']['followed'] != 0) {
            $query->filterFollowed();
        }
        if (array_key_exists('waiting_for_answer', $params['ReportSearch']) && $params['ReportSearch']['waiting_for_answer'] != 0) {
            $statusArray[] = Report::STATUS_WAITING_FOR_ANSWER;
        }
        if (array_key_exists('resolved', $params['ReportSearch']) && $params['ReportSearch']['resolved'] != 0) {
            $statusArray[] = Report::STATUS_RESOLVED;
        }
        if (array_key_exists('waiting_for_solution', $params['ReportSearch']) && $params['ReportSearch']['waiting_for_solution'] != 0) {
            $statusArray[] = Report::STATUS_WAITING_FOR_SOLUTION;
        }
        if (array_key_exists('waiting_for_response', $params['ReportSearch']) && $params['ReportSearch']['waiting_for_response'] != 0) {
            $statusArray[] = Report::STATUS_WAITING_FOR_RESPONSE;
        }
        if (array_key_exists('unresolved', $params['ReportSearch']) && $params['ReportSearch']['unresolved'] != 0) {
            $statusArray[] = Report::STATUS_UNRESOLVED;
        }
        if (array_key_exists('users_reports', $params['ReportSearch']) && $params['ReportSearch']['users_reports'] != 0) {
            $query->andFilterWhere([
                'report.user_id' => Yii::$app->user->id,
            ]);
        }

        if ($statusArray) {
            $query->andFilterWhere([
                'OR',
                ['in', 'report.status', $statusArray],
            ]);
        }

        switch ($customFilter) {
            case self::CUSTOM_FILTER_HIGHLIGHTED:
                $query = Report::find()->from(['u' => $query->filterHighlighted()])->orderBy(['id' => SORT_DESC]);

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                ]);
                break;
            case self::CUSTOM_FILTER_FRESH:
                $query->filterFresh();
                break;
            case self::CUSTOM_FILTER_NEARBY:
                if ($lat !== null && $long !== null) {
                    $query->filterNearby($lat, $long);
                }
                break;
            case self::CUSTOM_FILTER_FOLLOWED:
                $query->filterFollowed();
                break;
            default:
                break;
        }

        return $dataProvider;
    }

    public function hasFilterInSecondBlock()
    {
        return !empty($this->name) || !empty($this->report_category_id || !empty($this->institution_id));
    }
}
