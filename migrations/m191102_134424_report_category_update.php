<?php

use yii\db\Migration;
use app\models\db\ReportCategory;
use app\models\db\Report;
use app\models\db\ReportOriginal;

/**
 *
 */
class m191102_134424_report_category_update extends Migration
{
    const REPORT_CATEGORY_FORSAKEN_CAR = 3;
    const REPORT_CATEGORY_OTHER = 17;

    const REPORT_CATEGORY_MONUMENT = 9;
    const REPORT_CATEGORY_BUILDING = 20;
    const REPORT_CATEGORY_ILLEGAL_BUILDING = 24;
    const REPORT_CATEGORY_PUBLIC_ORDER = 21;

    /**
     * @var array
     */
    protected $obsoleteCategory = [
        self::REPORT_CATEGORY_MONUMENT => 'Emlékmű',
        self::REPORT_CATEGORY_BUILDING => 'Építkezés',
        self::REPORT_CATEGORY_ILLEGAL_BUILDING => 'Illegális építkezés',
        self::REPORT_CATEGORY_PUBLIC_ORDER => 'Közrend',
    ];


    /**
     *
     */
    public function up()
    {
        // Renaming "Elhagyatott autó" to "Elhagyatott jármű"
        $reportCategoryForsakenCar = ReportCategory::findOne(
            [
                'id' => self::REPORT_CATEGORY_FORSAKEN_CAR,
            ]
        );
        $reportCategoryForsakenCar->name = 'Elhagyott jármű';
        $reportCategoryForsakenCar->update();

        // Updating reports
        Report::updateAll(
            [
                'report_category_id' => self::REPORT_CATEGORY_OTHER,
            ],
            [
                'report_category_id' => $keys = array_keys($this->obsoleteCategory),
            ]
        );

        // Updating ReportOriginals
        ReportOriginal::updateAll(
            [
                'report_category_id' => self::REPORT_CATEGORY_OTHER,
            ],
            [
                'report_category_id' => $keys,
            ]
        );

        ReportCategory::deleteAll(
            [
                'id' => $keys,
            ]
        );
    }

    /**
     * Revoking deleted reportCategories upon something goes really wrong..
     */
    public function down()
    {
        $reportCategory = [];

        foreach ($this->obsoleteCategory as $identifier => $name) {
            $reportCategory[] = [
                'id' => $identifier,
                'name' => $name,
                'is_active' => 1,
            ];
        }

        Yii::$app->db->createCommand()->batchInsert(
            ReportCategory::tableName(),
            [
                'id',
                'name',
                'is_active',
            ],
            $reportCategory
        )->execute();
    }
}
