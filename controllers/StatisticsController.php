<?php

namespace app\controllers;

use app\models\forms\StatCityCategoryForm;
use app\models\forms\StatCityForm;
use app\models\forms\StatInstitutionCategoryForm;
use app\models\forms\StatInstitutionForm;
use Yii;
use yii\web\Controller;
use app\components\Header;
use app\components\helpers\Link;
use app\models\db\City;
use app\models\db\Institution;
use app\models\db\Report;
use app\models\db\ReportCategory;
use app\models\db\User;
use yii\helpers\ArrayHelper;

/**
 * Handles Statistics related actions.
 *
 * @package app\controllers
 */
class StatisticsController extends Controller
{
    public static $horizontalBarOnCompleteExpression = '
        function () {
            var ctx = this.chart.ctx;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            var chart = this;
            var datasets = this.config.data.datasets;

            datasets.forEach(function (dataset, i) {
                ctx.fontsize = "12px";
                ctx.fillStyle = "White";
                chart.getDatasetMeta(i).data.forEach(function (p, j) {
                    var value = datasets[i].data[j];
                    if(value > 0){
                        ctx.fillText(value, p._model.x - 20, p._model.y);
                    }
                });
            });
        }
    ';

    public static $verticalBarOnCompleteExpression = '
        function () {
            var ctx = this.chart.ctx;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            var chart = this;
            var datasets = this.config.data.datasets;

            datasets.forEach(function (dataset, i) {
                ctx.fontsize = "12px";
                ctx.fillStyle = "White";
                chart.getDatasetMeta(i).data.forEach(function (p, j) {
                    var value = datasets[i].data[j];
                    if(value > 0){
                        ctx.fillText(value, p._model.x, p._model.y + 20);
                    }
                });
            });
        }
    ';

    private $_defaultCityId;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Header::setAll([]);

            $this->_defaultCityId = ArrayHelper::getValue(Yii::$app->user, 'identity.city_id');

            return true;
        }

        return false;
    }

    /**
     * @param $cities City[]
     * @return integer
     */
    private function getDefaultCityIDToSelect($cities)
    {
        if ($this->_defaultCityId === null) {
            return Yii::$app->params['defaultCityId'];
        }

        return $this->_defaultCityId;
    }

    /**
     * Redirects the user to the cities subpage.
     *
     * @param string $citySlug [optional]
     *
     * @return string
     */
    public function actionIndex($citySlug = null)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.statistics.index'));

        /** @var array $cities */
        $cities = City::availableCities(true, false);
        $cityId = $this->getDefaultCityIDToSelect($cities);

        if ($citySlug !== null) {
            $cityId = City::getIdBySlug($citySlug);
        }

        $this->checkStatisticUrl($cityId, [Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES]);
    }

    /**
     * Renders the city statistics.
     *
     * @param null $citySlug [optional]
     *
     * @return string|\yii\web\Response
     */
    public function actionCities($citySlug = null)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.statistics.city'));

        if ($citySlug === null) {
            return $this->redirect('/statistics');
        }

        $cities = City::availableCities(true, false);
        $cityId = $this->getDefaultCityIDToSelect($cities);

        if ($citySlug !== null) {
            $cityId = City::getIdBySlug($citySlug);
        }

        $filterCityId = ArrayHelper::getValue(Yii::$app->request->get(), 'StatCityForm.city_id', '');

        if ($filterCityId != '') {
            $this->checkStatisticUrl($filterCityId, [Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES]);
        }

        $days = ArrayHelper::getValue(Yii::$app->request->get(), 'StatCityForm.days', 30);
        $limit = ArrayHelper::getValue(Yii::$app->request->get(), 'StatCityForm.limit', 10);
        $categoryLimit = ArrayHelper::getValue(Yii::$app->request->get(), 'StatCityCategoryForm.limit', 10);

        $resolvedStatistics = Institution::getStatistics($cityId, $limit, $days);
        $categoryStatistics = ReportCategory::getStatistics($cityId, null, $categoryLimit);

        $model = new StatCityForm(['city_id' => $cityId]);
        $model->load(Yii::$app->request->get());

        $model2 = new StatCityCategoryForm();
        $model2->load(Yii::$app->request->get());

        return $this->render('cities', [
            'model' => $model,
            'model2' => $model2,
            'cities' => $cities,
            'resolvedStatistics' => $resolvedStatistics,
            'categoryStatistics' => $categoryStatistics,
        ]);
    }

    /**
     * Renders the institution statistics.
     *
     * @param string $institutionSlug [optional]
     *
     * @return string|\yii\web\Response
     */
    public function actionInstitutions($institutionSlug = null)
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.statistics.institution'));

        $days = ArrayHelper::getValue(Yii::$app->request->get(), 'StatInstitutionForm.days', 30);
        $institutionId = ArrayHelper::getValue(Yii::$app->request->get(), 'StatInstitutionForm.institution_id', null);

        if ($institutionSlug !== null) {
            $institutionId = Institution::getIdBySlug($institutionSlug);
        }

        if ($institutionId == '') {
            $institutionId = null;
        }

        $categoryLimit = ArrayHelper::getValue(Yii::$app->request->get(), 'StatInstitutionCategoryForm.limit', 10);

        $reportStatistics = Report::getStatistics($institutionId, $days);

        $institutionCategoryStatistics = ReportCategory::getStatistics(null, $institutionId, $categoryLimit);
        $institutions = Institution::getInstitutions();

        $model = new StatInstitutionForm(['institution_id' => $institutionId]);
        $model->load(Yii::$app->request->get());

        $model2 = new StatInstitutionCategoryForm();
        $model2->load(Yii::$app->request->get());

        return $this->render('institutions', [
            'model' => $model,
            'model2' => $model2,
            'institutions' => $institutions,
            'reportStatistics' => $reportStatistics,
            'institutionCategoryStatistics' => $institutionCategoryStatistics,
        ]);
    }

    public function actionUsers()
    {
        Header::registerTag(Header::TYPE_TITLE, Yii::t('meta', 'title.statistics.user'));

        $ranks = User::getRanks();
        $ranksMonth = User::getCurrentMonthRanks();

        return $this->render('users', [
            'ranks' => $ranks,
            'ranksMonth' => $ranksMonth,
        ]);
    }

    /**
     * @param int $cityId
     *
     * @param array $prePath [optional] the path to put before the generated url.
     *
     * @return void
     */
    private function checkStatisticUrl($cityId, $prePath = [])
    {
        $validUrl = City::getStatisticUrl($cityId, $prePath);

        $validUrl = $this->extendUrlWithParams($validUrl);

        if ($validUrl === false) {
            $this->redirect(Link::to(Link::HOME), 301);
            Yii::$app->end();
        }

        if (Link::to([Yii::$app->request->pathInfo]) !== $validUrl) {
            $this->redirect($validUrl, 301);
            Yii::$app->end();
        }
    }

    /**
     * @param $validUrl
     * @return string
     */
    private function extendUrlWithParams($validUrl)
    {
        $params = Yii::$app->request->get();

        if (empty($params)) {
            return $validUrl;
        }

        ArrayHelper::remove($params, 'citySlug');

        if (isset($params['StatCityForm']['city_id'])) {
            unset($params['StatCityForm']['city_id']);
        }

        if (!empty($params)) {
            $validUrl .= '?' . http_build_query($params);
        }

        return $validUrl;
    }
}
