<?php

namespace app\modules\legacyapi\controllers;

use Yii;
use app\modules\legacyapi\components\ApiController;
use app\models\db\Report;
use app\models\db\ReportActivity;
use app\models\db\ReportAttachment;
use app\components\helpers\Html;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\web\HttpException;
use yii\helpers\Url;

class ReportsController extends ApiController
{
    private static $_statuses;

    private static $statusMappingOldNew = [
        0 => Report::STATUS_NEW,
        1 => Report::STATUS_RESOLVED,
        2 => Report::STATUS_UNRESOLVED,
        3 => Report::STATUS_WAITING_FOR_RESPONSE,
        4 => Report::STATUS_DELETED,
        7 => Report::STATUS_WAITING_FOR_SOLUTION,
    ];

    private static $statusMappingNewOld = [
        Report::STATUS_RESOLVED => 1,
        Report::STATUS_UNRESOLVED => 2,
        Report::STATUS_WAITING_FOR_RESPONSE => 3,
        Report::STATUS_WAITING_FOR_INFO => 3,
        Report::STATUS_WAITING_FOR_SOLUTION => 7,
        Report::STATUS_WAITING_FOR_ANSWER => 3,
    ];

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['POST', 'GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
        ];
    }

    public function init()
    {
        self::$_statuses = \app\models\db\Report::getPublicStatuses();
        return parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['pageCache']['enabled'] = !YII_DEBUG;

        return $behaviors;
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
            ->filterAvailable()
            ->orderBy(['updated_at' => SORT_DESC])
            ->with(['user', 'institution', 'district']);

        $request = Yii::$app->request;
        if ($request->get('mesto')) {
            $query->andFilterWhere(['city_id' => (int)$request->get('mesto')]);
        }
        if ($request->get('mestska_cast')) {
            $query->andFilterWhere(['district_id' => (int)$request->get('mestska_cast')]);
        }
        if (trim(strip_tags($request->get('search')))) {
            $query->andFilterWhere([
                'OR',
                ['like', 'report.name', trim(strip_tags($request->get('search')))],
                ['like', 'report.description', trim(strip_tags($request->get('search')))],
                ['like', 'report.user_location', trim(strip_tags($request->get('search')))],
                ['like', 'report.street_name', trim(strip_tags($request->get('search')))],
            ]);
        }
        if ($request->get('user')) {
            $query->andFilterWhere([
                'user_id' => (int)$request->get('user'),
                'anonymous' => 0,
            ]);
        }
        if ($request->get('status')) {
            $newStatusId = ArrayHelper::getValue(self::$statusMappingOldNew, $request->get('status'), Report::STATUS_WAITING_FOR_ANSWER);
            $query->andFilterWhere(['status' => $newStatusId]);
        }
        if ($request->get('near')) {
            $userLocation = static::parseLocationCoords($request->get('near'));
            if (!empty($userLocation['latitude']) && !empty($userLocation['longitude'])) {
                $query->filterNearby($userLocation['latitude'], $userLocation['longitude']);
            }
            $showDistance = true;
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSizeLimit' => [1, 15],
            'pageSizeParam' => 'limit',
        ]);
        $list = $query->orderBy(['report.id' => SORT_DESC])
            ->offset($pagination->offset)
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
                $distance = 'unknown';
            }

            $commonData = static::getCommonData($item);
            $commonData['distance'] = $distance;
            $items[] = $commonData;
        }
        $reports = [
            'pocet_podnetov' => $pagination->totalCount,
            'pocet_stran' => $pagination->pageCount,
            'strana' => (string)($pagination->getPage() + 1),
        ];

        // Empty 'podnety' array cause infinite loading on iPhone apps
        if (count($items) > 0) {
            $reports['podnety'] = $items;
        }

        return $reports;
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
            ])
            ->filterAvailable()
            ->with(['user', 'institution', 'district'])
            ->one();

        if ($item === null) {
            return [
                'historia' => [],
            ];
        }

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
                $images = [];
                $attachments = $activity->reportAttachments;
                if (count($attachments) > 0) {
                    /* @var $attachment \app\models\db\ReportAttachment */
                    foreach ($attachments as $attachment) {
                        if ($attachment->type === ReportAttachment::TYPE_COMMENT_PICTURE) {
                            $images[] = Url::base(true) . $attachment->getAttachmentUrl();
                        }
                    }
                }

                $isOwnerActivity = $activity->report->user_id == $activity->user_id;

                switch ($activity->type) {
                    case ReportActivity::TYPE_COMMENT:
                        $name = $isOwnerActivity && $activity->report->anonymous === 1 ? Yii::t('data', 'report.anonymous') : ArrayHelper::getValue($activity, 'user.fullName', Yii::t('data', 'report.anonymous'));
                        $type = 'komentar';
                        break;
                    case ReportActivity::TYPE_ANSWER:
                        $name = Yii::t('report', 'response_by_institution');
                        $type = 'odpoved';
                        break;
                    default:
                        $name = '';
                        $type = 'komentar';
                        break;
                }

                $activities[] = [
                    'id' => $activity->id,
                    'typ' => $type,
                    'obsah' => [
                        'meno'      => $name,
                        'text'      => HTMLPurifier::process($activity->comment),
                        'images'    => $images,
                        'datum'     => Yii::$app->formatter->asDate($activity->created_at),
                        'cas'       => Yii::$app->formatter->asDatetime($activity->created_at),
                        'timestamp' => $activity->created_at,
                        'icon'      => $activity->getPictureUrl(false),
                    ],
                ];
            }
        }

        $_images = $item->getPictures();
        $images = [];
        foreach ($_images as $image) {
            $images[] = Url::base(true) . $image['url'];
        }

        $commonData = static::getCommonData($item);
        return ArrayHelper::merge(
            $commonData,
            [
                'images' => $images,
                'historia' => $activities,
            ]
        );
    }

    public function actionMapdata($status = null, $kategoria = null)
    {
        $reports = Report::find()
            ->select([
                'id',
                'heading' => new Expression('report.name'),
                'description' => new Expression('IF(CHAR_LENGTH(report.description)>64, CONCAT(SUBSTRING(report.description, 1, 64), "..."), report.description)'),
                'map_x' => 'report.latitude',
                'map_y' => 'report.longitude',
                'status' => new Expression('CASE
                    WHEN status = :status_0 THEN :status_0_new
                    WHEN status = :status_1 THEN :status_1_new
                    WHEN status = :status_2 THEN :status_2_new
                    WHEN status = :status_3 THEN :status_3_new
                    WHEN status = :status_4 THEN :status_4_new
                    WHEN status = :status_5 THEN :status_5_new
                    END
                ', [
                // @codingStandardsIgnoreStart
                    ':status_0' => Report::STATUS_RESOLVED,
                    ':status_0_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_RESOLVED),
                    ':status_1' => Report::STATUS_UNRESOLVED,
                    ':status_1_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_UNRESOLVED),
                    ':status_2' => Report::STATUS_WAITING_FOR_RESPONSE,
                    ':status_2_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_WAITING_FOR_RESPONSE),
                    ':status_3' => Report::STATUS_WAITING_FOR_INFO,
                    ':status_3_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_WAITING_FOR_INFO),
                    ':status_4' => Report::STATUS_WAITING_FOR_SOLUTION,
                    ':status_4_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_WAITING_FOR_SOLUTION),
                    ':status_5' => Report::STATUS_WAITING_FOR_ANSWER,
                    ':status_5_new' => ArrayHelper::getValue(self::$statusMappingNewOld, Report::STATUS_WAITING_FOR_ANSWER),
                ]),
                // @codingStandardsIgnoreEnd
            ])
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['report_category_id' => $kategoria])
            ->filterAvailable()
            ->asArray()
            ->all();

        return $reports;
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
            'detail_url' => Url::to(['reports/view', 'id' => $item->id], true),
            'nadpis' => $item->name,
            'image' => str_replace(Url::base(true) . '/', '', $item->getShareImage()),
            'popis' => HtmlPurifier::process($item->description),
            'datum' => Yii::$app->formatter->asDatetime($item->created_at),
            'status_slovom' => ArrayHelper::getValue(self::$_statuses, $item->status),
            'status_id' => ArrayHelper::getValue(self::$statusMappingNewOld, $item->status),
            'ulica' => $item->street_name,
            'zodpovednost' => ArrayHelper::getValue($item, 'institution.name'),
            'url' => $item->getUrl(),
            'lat' => $item->latitude,
            'long' => $item->longitude,
            'user_name' => $item->anonymous === 1 ? Yii::t('data', 'report.anonymous') : ArrayHelper::getValue($item, 'user.fullName'),
            'user_id' => $item->anonymous === 1 ? 0 : ArrayHelper::getValue($item, 'user.id', 0),
            'mesto_slovom' => ArrayHelper::getValue($item, 'district.name'), // should be city.name
            'mesto_id' => $item->city_id,
            'mestska_cast_id' => $item->district_id,
            'mestska_cast_slovom' => ArrayHelper::getValue($item, 'district.name'),
            'distance' => Yii::$app->request->get('mylocation') ? static::parseLocationCoords(Yii::$app->request->get('mylocation')) : 'unknown',
            'comments' => $item->getCommentCount(),
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

        $R = 6371; // earth's radius (mean radius = 6371km)
        $x = ($l2 - $l1) * cos(($r1 + $r2) / 2);
        $y = ($r2 - $r1);
        $d = sqrt($x * $x + $y * $y) * $R;

        return round($d, 2);
    }

    protected static function parseLocationCoords($coords)
    {
        $parsedCoords = ['latitude' => null, 'longitude' => null];
        list($parsedCoords['latitude'], $parsedCoords['longitude']) = explode(',', $coords);

        return $parsedCoords;
    }
}
