<?php

namespace app\models\db\query;

use app\models\db\Report;
use app\models\db\ReportActivity;
use Yii;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Report]].
 *
 * @see Report
 */
class ReportQuery extends \yii\db\ActiveQuery
{
    public function filterPublic()
    {
        return $this->andWhere([
            'report.status' => array_keys(Report::getPublicStatuses()),
        ]);
    }

    public function filterAvailable()
    {
        return $this->andWhere([
            'NOT IN',
            'report.status',
            [
                Report::STATUS_NEW,
                Report::STATUS_DELETED,
                Report::STATUS_EDITING,
                Report::STATUS_DRAFT,
            ],
        ]);
    }

    public function filterNotDeleted()
    {
        return $this->andWhere([
            'NOT IN',
            'status',
            [
                Report::STATUS_DELETED,
            ],
        ]);
    }

    public function filterNotDeletedOrDaft()
    {
        return $this->andWhere([
            'NOT IN',
            'status',
            [
                Report::STATUS_DELETED,
                Report::STATUS_DRAFT,
            ],
        ]);
    }

    public function filterResolved()
    {
        return $this->andWhere([
            'report.status' => [Report::STATUS_RESOLVED],
        ]);
    }

    public function filterClosed()
    {
        return $this->andWhere([
            'report.status' => [Report::STATUS_RESOLVED, Report::STATUS_UNRESOLVED, Report::STATUS_DELETED],
        ]);
    }

    public function filterUnresolved()
    {
        return $this->andWhere([
            'report.status' => [Report::STATUS_UNRESOLVED],
        ]);
    }

    public function filterNew()
    {
        return $this->andWhere([
            'report.status' => [Report::STATUS_NEW],
        ]);
    }

    public function filterNotResolved()
    {
        return $this->andWhere([
            'NOT IN',
            'report.status',
            [
                Report::STATUS_RESOLVED,
                Report::STATUS_UNRESOLVED,
                Report::STATUS_DELETED,
                Report::STATUS_DRAFT,
            ],
        ]);
    }

    public function filterDraft()
    {
        return $this->andWhere([
            'report.status' => [Report::STATUS_DRAFT],
        ]);
    }

    public function filterInProgress()
    {
        return $this->andWhere([
            'report.status' => [
                Report::STATUS_NEW,
                Report::STATUS_EDITING,
                Report::STATUS_WAITING_FOR_ANSWER,
                Report::STATUS_WAITING_FOR_INFO,
                Report::STATUS_WAITING_FOR_RESPONSE,
                Report::STATUS_WAITING_FOR_SOLUTION,
            ],
        ]);
    }

    public function filterInDecision()
    {
        return $this->andWhere([
            'report.status' => [
                Report::STATUS_WAITING_FOR_ANSWER,
                Report::STATUS_WAITING_FOR_RESPONSE,
            ],
        ]);
    }

    public function filterNotThis($id)
    {
        return $this->andWhere('report.id<>:id', [':id' => $id]);
    }

    public function filterHighlighted()
    {
        $originalQuery = clone $this;
        $popularReports = $originalQuery->join(
            'LEFT JOIN',
            ReportActivity::tableName(),
            'report_activity.report_id=report.id AND report_activity.type=:type_comment',
            [':type_comment' => ReportActivity::TYPE_COMMENT]
        )
            ->groupBy(['report.id'])
            ->andHaving([
                '>=',
                'COUNT(report_activity.id)',
                10,
            ])
            ->filterIn2Months();

        return $this
            ->andWhere(['highlighted' => 1])
            ->union($popularReports);
    }

    public function filterFresh()
    {
        return $this->andWhere(['>=', 'report.created_at', strtotime('- 1months')]);
    }

    public function filterFollowed()
    {
        $userId = Yii::$app->user->id;

        if ($userId !== null) {
            return $this->andWhere(new Expression('id IN (SELECT report_id FROM report_following WHERE user_id = :userid)', ['userid' => $userId]));
        }

        return $this;
    }

    public function filterNearby($lat, $long)
    {
        $radius = 1; // km
        $R = 6371; // radius of earth in km

        $maxLat = $lat + rad2deg($radius / $R);
        $minLat = $lat - rad2deg($radius / $R);
        $maxLong = $long + rad2deg(asin($radius / $R) / cos(deg2rad($lat)));
        $minLong = $long - rad2deg(asin($radius / $R) / cos(deg2rad($lat)));

        $subQuery = Report::find()
            ->where(['BETWEEN', 'report.latitude', $minLat, $maxLat])
            ->andWhere(['BETWEEN', 'report.longitude', $minLong, $maxLong]);

        $radiusCalcuation = 'acos(sin(:lat)*sin(radians(report.latitude)) + cos(:lat)*cos(radians(report.latitude))*cos(radians(report.longitude)-:long)) * :R';

        return $this->addSelect([
            'report.*',
            'D' => new Expression($radiusCalcuation, [
                ':lat' => deg2rad($lat),
                ':long' => deg2rad($long),
                ':R' => $R,
            ]),
        ])->from([
            'report' => $subQuery,
        ])->andWhere(new Expression($radiusCalcuation . ' < :radius', [
            ':lat' => deg2rad($lat),
            ':long' => deg2rad($long),
            ':R' => $R,
            ':radius' => $radius,
        ]));
    }

    public function filterIn2Months()
    {
        return $this->andWhere(['>=', 'report.created_at', strtotime('- 2months')]);
    }

    public function andBelongsToActualUser()
    {
        if (!Yii::$app->user->isGuest) {
            $this->andWhere(['report.user_id' => Yii::$app->user->id]);
        }

        return $this;
    }

    public function orBelongsToActualUser()
    {
        if (!Yii::$app->user->isGuest) {
            $this->orWhere(['report.user_id' => Yii::$app->user->id]);
        }

        return $this;
    }

    public function filterNotAnonymous()
    {
        $this->andWhere(['report.anonymous' => 0]);

        return $this;
    }
}
