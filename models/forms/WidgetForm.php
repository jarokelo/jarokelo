<?php

namespace app\models\forms;

use app\models\db\City;
use app\models\db\District;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

class WidgetForm extends Model
{
    /**
     * @var string
     */
    public $city_id;
    public $district_id;
    public $institution_id;
    public $status;
    public $report_category_id;
    public $size;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'district_id', 'institution_id', 'status', 'report_category_id'], 'safe'],
            [['size'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city_id' => Yii::t('data', 'report.city_id'),
            'report_category_id' => Yii::t('data', 'report.report_category_id'),
            'institution_id' => Yii::t('data', 'report.institution_id'),
            'district_id' => Yii::t('data', 'report.district_id'),
            'status' => Yii::t('data', 'report.status'),
            'size' => Yii::t('data', 'report.report_count'),
        ];
    }

    public function getIframeUrl()
    {
        $widgetSlug = [];
        if ($this->city_id !== null) {
            $city = City::findOne($this->city_id);
            if ($city !== null) {
                $widgetSlug[] = $city->slug;
            }
        }
        if ($this->district_id !== null) {
            $district = District::findOne($this->district_id);
            if ($district !== null) {
                $widgetSlug[] = $district->slug;
            }
        }
        $params = [
            'location' => implode('/', $widgetSlug) ?: null,
            'status' => $this->status,
            'category' => $this->report_category_id,
            'institution' => $this->institution_id,
            'limit' => $this->size,
        ];

        $url = Url::to(['/widget/index'], true);
        $query = http_build_query($params);
        if ($params) {
            $url .= '?' . $query;
        }

        return $url;
    }
}
