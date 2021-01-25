<?php
/**
 * @var \Yii\Web\View $this
 * @var \app\models\db\search\ReportSearch $searchModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

use yii\web\View;
use yii\helpers\Json;
use app\components\helpers\Link;
use app\assets\MapboxAdvancedAsset;

$this->registerJsFile(
    'https://maps.googleapis.com/maps/api/js?key=' . Yii::$app->params['google']['api_key_http'] . '&libraries=places&callback=ReportsOnMap.initMap',
    [
        'async' => true,
        'defer' => true,
        'depends' => [
            MapboxAdvancedAsset::className(),
        ],
    ]
);
$cityLat = \yii\helpers\ArrayHelper::getValue($searchModel, 'city.latitude', Yii::$app->params['map']['defaultPosition']['lat']);
$cityLng = \yii\helpers\ArrayHelper::getValue($searchModel, 'city.longitude', Yii::$app->params['map']['defaultPosition']['lng']);

$districtLat = \yii\helpers\ArrayHelper::getValue($searchModel, 'district.latitude');
$districtLng = \yii\helpers\ArrayHelper::getValue($searchModel, 'district.longitude');

$split = preg_split('@/@', Yii::$app->request->referrer);
$this->registerJs('window.reportsOnMapInitData = ' . Json::encode([
    'zoom' => 12,
    'center' => [
        'lng' => \app\models\db\Report::formatCoordinate((int)$districtLng != 0 ? $districtLng : $cityLng),
        'lat' => \app\models\db\Report::formatCoordinate((int)$districtLat != 0 ? $districtLat : $cityLat),
    ],
    'mapTypeControl' => false,
    'streetViewControl' => true,
    'fullscreenControl' => true,
    'isHomeReferrer' => isset($split[3]) && $split[3] === '',
]) . ';', View::POS_END);
?>
<div class="container">
    <?= $this->render('_map_search', [
        'model' => $searchModel,
        'type' => \app\components\helpers\Link::MAP,
    ]); ?>
</div>

<?php if (!count($dataProvider->getModels())): ?>
    <?=$this->render('@app/views/_snippets/_no-reports-found.php', [
        'link' => Link::to(Link::MAP),
    ])?>
<?php return;
endif; ?>
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
            <div id="map" class="reportsonmap__map">
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

<?=$this->render('/_snippets/_hero-bottom')?>
