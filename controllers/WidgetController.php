<?php

namespace app\controllers;

use app\models\db\District;
use Yii;
use app\components\Header;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\models\db\Institution;
use app\models\db\Report;
use app\models\db\City;
use app\models\forms\WidgetForm;

/**
 * Handles the rendering of the configurable widget.
 *
 * @package app\controllers
 */
class WidgetController extends Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            return true;
        }

        return false;
    }

    /**
     * @param null $widget
     * @param null $mestska_cast
     * @param null $mesto
     * @param null $status
     * @param null $kategoria
     * @param null $zodpovednost
     * @param null $limit
     * @param null $category
     * @param $institution
     * @return string
     */
    public function actionIndex($widget = null, $mestska_cast = null, $mesto = null, $status = null, $kategoria = null, $zodpovednost = null, $limit = null, $location = null, $category = null, $institution = null)
    {
        $this->layout = '//main-widget';

        $location = $widget ?: $location;
        $category = $kategoria ?: $category;
        $institution = $zodpovednost ?: $institution;
        $district = $mestska_cast;

        Header::registerTag(Header::TYPE_TITLE, Yii::t('label', 'footer.know_more.widget'));

        $citySlug = ArrayHelper::getValue(explode('/', $location), 0, $location);
        $districtSlug = ArrayHelper::getValue(explode('/', $location), 1, $district);
        $limit = ArrayHelper::remove($filter, 'size', 10);

        $filter = [
            'city_id' => City::getIdBySlug($citySlug) ?: null,
            'district_id' => District::getIdBySlug($districtSlug) ?: null,
            'status' => $status,
            'report_category_id' => $category,
            'institution_id' => $institution,
        ];

        $reports = Report::getLatest($limit, $filter);

        return $this->render('index', [
            'reports' => $reports,
        ]);
    }

    public function actionConfigure()
    {
        $model = new WidgetForm(['city_id' => 1]);
        $model->load(Yii::$app->request->post());

        Header::registerTag(Header::TYPE_TITLE, Yii::t('label', 'footer.know_more.widget'));

        $cities = City::availableCities(true, false);
        $institutions = [];

        $tempInstitutions = Institution::getInstitutions($model->city_id);
        foreach ($tempInstitutions as $temp) {
            $institutions[$temp->id] = $temp->name;
        }

        return $this->render('configure', [
            'institutions' => $institutions,
            'cities' => $cities,
            'model' => $model,
        ]);
    }
}
