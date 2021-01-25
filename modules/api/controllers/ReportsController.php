<?php

namespace app\modules\api\controllers;

use Yii;
use app\modules\api\components\ApiController;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\components\helpers\Html;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\helpers\Url;

class ReportsController extends ApiController
{
    private static $_statuses;
    private static $_statusIds;

    const DESIRED_DISTANCE_IN_METER = 500;
    const EARTH_RADIUS_IN_KM = 6371;

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
        ];
    }

    public function init()
    {
        self::$_statuses = Report::getPublicStatuses();
        self::$_statusIds = array_keys(self::$_statuses);
        return parent::init();
    }

    /**
     * Returns all reports
     *
     * @return array|mixed
     */
    public function actionIndex()
    {
        $showDistance = false;

        $query = Report::find()
            ->where([
                'status' => self::$_statusIds,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->with(['user', 'institution', 'district']);

        $request = Yii::$app->request;
        if ($request->get('city')) {
            $query->andFilterWhere(['city_id' => (int)$request->get('city')]);
        }
        if ($request->get('district')) {
            $query->andFilterWhere(['district_id' => (int)$request->get('district')]);
        }
        if (trim(strip_tags($request->get('term')))) {
            $query->andFilterWhere(['like', 'name', trim(strip_tags($request->get('term')))]);
        }
        if ($request->get('user')) {
            $query->andFilterWhere([
                'user_id' => (int)$request->get('user'),
                'anonymous' => 0,
            ]);
        }
        if ($request->get('status') && in_array($request->get('status'), self::$_statusIds)) {
            $query->andFilterWhere([
                'status' => (int)$request->get('status'),
            ]);
        }
        if ($request->get('near')) {
            $userLocation = static::parseLocationCoords($request->get('near'));
            if (!empty($userLocation['latitude']) && !empty($userLocation['longitude'])) {
                $arcLengthDistance = self::DESIRED_DISTANCE_IN_METER / 2 * 360 / (self::EARTH_RADIUS_IN_KM * 2 * M_PI * 1000);
                $query->andWhere(new Expression('ABS(:userLatitude - latitude)<=:maxLatitude and ABS(:userLongitude - longitude)<=:maxLongitude', [
                    ':userLatitude' => $userLocation['latitude'],
                    ':userLongitude' => $userLocation['longitude'],
                    ':maxLatitude' => $arcLengthDistance,
                    ':maxLongitude' => $arcLengthDistance,
                ]));
                $query->orderBy('ABS(' . $userLocation['latitude'] . ' - latitude) + ABS(' . $userLocation['longitude'] . ' - longitude) ASC');
            }
            $showDistance = true;
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSizeLimit' => [1, 20],
            'pageSizeParam' => 'limit',
        ]);
        $list = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        if ($request->get('mylocation')) {
            $userLocation = static::parseLocationCoords($request->get('mylocation'));
            $showDistance = true;
        }

        $items = [];
        /* @var $item \app\models\db\Report */
        foreach ($list as $item) {
            if ($showDistance === true) {
                $distance = static::getDistance($item, $userLocation);
            } else {
                $distance = '';
            }

            $commonData = static::getCommonData($item);
            $commonData['distance'] = $distance;
            $items[] = $commonData;
        }
        return [
            'items' => $items,
            'pagination' => $pagination->getLinks(true),
        ];
    }

    /**
     * Returns details of a report
     *
     * @param $id
     * @return array|mixed
     * @throws \yii\web\HttpException when city not found or inactive
     */
    public function actionView($id)
    {
        /* @var $item \app\models\db\Report */
        $item = Report::find()
            ->where([
                'id' => $id,
                'status' => self::$_statusIds,
            ])
            ->with(['user', 'institution', 'district'])
            ->one();

        if (!$item) {
            throw new HttpException(404, 'Report not found or inactive.');
        }

        $request = Yii::$app->request;

        $_activities = $item
            ->getReportActivities()
            ->where(['type' => [ReportActivity::TYPE_ANSWER, ReportActivity::TYPE_COMMENT], 'visible' => 1])
            ->with(['user'])
            ->orderBy('created_at')
            ->limit(100)
            ->all();

        $activities = [];
        if (count($_activities) > 0) {
            /* @var $activity \app\models\db\ReportActivity */
            foreach ($_activities as $activity) {
                switch ($activity->type) {
                    case ReportActivity::TYPE_COMMENT:
                        $user_name = ($activity->user_id == $activity->report->user_id && $activity->report->anonymous) ? Yii::t('data', 'report.anonymous') : $activity->user->getFullName();
                        break;
                    case ReportActivity::TYPE_ANSWER:
                        $user_name = Yii::t('report', 'response_by_institution');
                        break;
                    default:
                        $user_name = '';
                        break;
                }

                $activities[] = [
                    'id'         => $activity->id,
                    'user'       => $user_name,
                    'type'       => $activity->type,
                    'created_at' => $activity->created_at,
                    'comment'    => Html::formatText($activity->comment),
                ];
            }
        }

        $commonData = static::getCommonData($item);
        return ArrayHelper::merge(
            $commonData,
            [
                'description' => Html::formatText($item->description),
                'media' => $item->getPicturesAndVideos(true),
                'activity' => $activities,
                'distance' => $request->get('mylocation') ? static::parseLocationCoords($request->get('mylocation')) : '',
            ]
        );
    }

    /**
     * Returns the array of the common keys
     * @param $item \app\models\db\Report
     * @return array
     */
    protected static function getCommonData($item)
    {
        return [
            'id' => $item->id,
            'title' => $item->name,
            'image' => $item->getShareImage(),
            'created' => $item->created_at,
            'updated' => $item->updated_at,
            'user' => $item->anonymous === 1 ? Yii::t('data', 'report.anonymous') : $item->user->getFullName(),
            'category' => [
                'id' => $item->report_category_id,
                'name' => $item->reportCategory->name,
            ],
            'institution' => [
                'id' => $item->institution_id,
                'name' => ArrayHelper::getValue($item, 'institution.name'),
            ],
            'status' => [
                'id' => $item->status,
                'description' => ArrayHelper::getValue(self::$_statuses, $item->status),
            ],
            'address' => [
                'full_address' => $item->user_location,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'zoom' => $item->zoom,
                'city' => [
                    'id' => $item->city_id,
                    'name' => ArrayHelper::getValue($item, 'city.name'),
                ],
                'district' => [
                    'id' => $item->district_id,
                    'name' => ArrayHelper::getValue($item, 'district.name'),
                ],
                'street_name' => $item->street_name,
                'post_code' => $item->post_code,
            ],
            'url' => Url::to(['reports/view', 'id' => $item->id], true),
        ];
    }

    /**
     * @param $item \app\models\db\Report
     * @param $myLocation array
     * @return float
     */
    protected static function getDistance($item, $myLocation)
    {
        if (!$item || !$item->latitude || !$item->longitude || !$myLocation['latitude'] || !$myLocation['longitude']) {
            return null;
        }

        $r1 = deg2rad(floatval($item->latitude));
        $l1 = deg2rad(floatval($item->longitude));
        $r2 = deg2rad(floatval($myLocation['latitude']));
        $l2 = deg2rad(floatval($myLocation['longitude']));

        // Pythagoras' theorem is used on an equirectangular projection
        // x = Δλ ⋅ cos φm
        // y = Δφ
        // d = R ⋅ √(x² + y²)

        $R = self::EARTH_RADIUS_IN_KM;
        $x = ($l2 - $l1) * cos(($r1 + $r2) / 2);
        $y = ($r2 - $r1);
        $d = sqrt($x * $x + $y * $y) * $R;

        return round($d, 2);
    }

    protected static function parseLocationCoords($coords)
    {
        $parsedCoords = ['latitude' => null, 'longitude' => null];
        $coordsArray = explode(',', $coords);
        if (count($coordsArray) == 2 && !empty($coordsArray[0]) && !empty($coordsArray[1]) && is_numeric($coordsArray[0]) && is_numeric($coordsArray[1])) {
            $parsedCoords['latitude'] = floatval($coordsArray[0]);
            $parsedCoords['longitude'] = floatval($coordsArray[1]);
        }
        return $parsedCoords;
    }
}
