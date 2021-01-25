<?php
use yii\web\View;
use yii\helpers\Json;
use app\components\helpers\Link;
use yii\helpers\Html;

/* @var \app\models\db\search\ReportSearch $searchModel */

$this->registerJsFile(
    'https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['google']['api_key_http'] . '&libraries=places&callback=site.ReportsOnMap.initMap',
    [
        'defer' => true,
        'depends' => [
            yii\web\JqueryAsset::className(),
            app\assets\AppAsset::className(),
        ],
    ]
);

$cityLat = \yii\helpers\ArrayHelper::getValue($searchModel, 'city.latitude', Yii::$app->params['map']['defaultPosition']['lat']);
$cityLng = \yii\helpers\ArrayHelper::getValue($searchModel, 'city.longitude', Yii::$app->params['map']['defaultPosition']['lng']);

$districtLat = \yii\helpers\ArrayHelper::getValue($searchModel, 'district.latitude');
$districtLng = \yii\helpers\ArrayHelper::getValue($searchModel, 'district.longitude');

$this->registerJs('site.ReportsOnMap.mapData = ' . Json::encode([
    'zoom' => 12,
    'center' => [
        'lat' => \app\models\db\Report::formatCoordinate((int)$districtLat != 0 ? $districtLat : $cityLat),
        'lng' => \app\models\db\Report::formatCoordinate((int)$districtLng != 0 ? $districtLng : $cityLng),
    ],
    'mapTypeControl' => false,
    'streetViewControl' => true,
    'fullscreenControl' => true,
]) . ';', View::POS_END);

?>

<div class="container">
    <?= $this->render('_map_search', [
        'model' => $searchModel,
        'type' => \app\components\helpers\Link::MAP,
    ]); ?>
</div>

<?php if (count($dataProvider->getModels()) > 0): ?>
<div class="container reportsonmap offset--bottom">
    <div class="row">
        <div class="col-xs-12 col-lg-5" id="map-report-list">
            <?php
            foreach ($dataProvider->getModels() as $report) {
                echo $this->render('_card', [
                    'report' => $report,
                    'showLatLngAsData' => true,
                    'wideOnMobile' => true,
                ]);
            }
            ?>
        </div>
        <div class="col-xs-12 col-lg-7 first-xs last-lg">
            <div class="reportsonmap__map">
                <div class="reportsonmap__map__media"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <div class="pagination">
                <?= app\components\LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('/_snippets/_hero-bottom') ?>

<?php else: ?>
    <?= $this->render('@app/views/_snippets/_no-reports-found.php', ['link' => Link::to(Link::MAP)]) ?>
<?php endif; ?>