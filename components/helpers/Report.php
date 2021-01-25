<?php

namespace app\components\helpers;

use Yii;
use \app\assets\AppAsset;

class Report
{
    /**
     * Returns a cooperating partner info for a Report.
     *
     * @param \app\models\db\Report $model
     *
     * @return bool|array
     */
    public static function getPartner(\app\models\db\Report $model)
    {
        $bundleUrl = AppAsset::register(Yii::$app->view)->baseUrl;

        $partner = [
            'logo' => null,
            'text' => null,
            'url' => null,
        ];

        if ($model->city->slug == 'slug') {
            $partner['id'] = 'ID';
            $partner['logo'] = $bundleUrl . '/images/institution/example.png';
            $partner['title'] = Yii::t('report', 'coop_partner.0.title');
            $partner['text'] = Yii::t('report', 'coop_partner.0.text');
            $partner['url'] = 'http://www.example.hu/';
        }

        if (isset($partner['logo'], $partner['title'], $partner['text'])) {
            return $partner;
        }

        return false;
    }
}
