<?php

use app\models\db\Institution;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\components\widgets\Pjax;
use app\models\db\Report;
use app\assets\MapLayerAsset;
use app\models\db\MapLayer;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */

$this->title = Yii::t('report', 'update.title');
$this->params['breadcrumbs'] = [['url' => ['reports/view', 'id' => $model->id], 'label' => $model->name], $this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['report/index'], 'label' => Yii::t('menu', 'report')];

if ($mapLayers = $model->getMapLayers()):
    MapLayerAsset::register($this);
endif;

echo $this->render('@app/views/_snippets/_mapbox', [
    'options' => [
        'zoom' => intval($model->zoom),
        'selectors' => [
            'zoom' => '#report-zoom',
            'map' => '#map',
            'latitude' => '#report-latitude',
            'longitude' => '#report-longitude',
            'user_location' => '#report-user_location',
            'post_code' => '#report-post_code',
            'address' => '#report-address',
            'street_name' => '#report-street_name',
        ],
        'center' => [
            'lat' => \app\models\db\Report::formatCoordinate($model->latitude),
            'lng' => \app\models\db\Report::formatCoordinate($model->longitude),
        ],
        'locationChangeHandler' => true,
        'isAdmin' => true,
    ] + compact('mapLayers'),
]);

echo $this->render('_image_editor');

// set reportId and adminId hidden fields for the websocket locker script
echo Html::hiddenInput('reportId', $model->id, ['id' => 'reportId']);
echo Html::hiddenInput('adminId', Yii::$app->user->identity->id, ['id' => 'adminId']);

?>

<div class="update-report-form block--grey">
    <?php $form = ActiveForm::begin([
        'id' => 'report-update',
        'action' => ['report/update', 'id' => $model->id],
    ]) ?>

    <div class="row">
        <div class="col-md-7"><h3><?= $this->title ?></h3></div>
        <div class="col-md-5">
            <?= \app\models\db\ReportOriginal::find(['report_id' => $model->id])->exists() ? Html::a(
                Yii::t('report', 'update.compare'),
                Url::to(['report/compare', 'id' => $model->id]),
                [
                    'class' => 'pull-right btn btn-info btn-compare-content',
                    'data-target' => '#compare-modal',
                    'data-toggle' => 'modal',
                ]
            ) : '' ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <?= $form->field($model, 'report_category_id')->widget(Select2::className(), [
                'data' => \app\models\db\ReportCategory::getList(),
                'theme' => Select2::THEME_KRAJEE,
                'options' => [
                    'id' => 'report-category-select2',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <?= $form->field($model, 'institution_id')->widget(Select2::className(), [
                'data' => Institution::getInstitutionsWeighted($model->city_id, false, $model),
                'theme' => Select2::THEME_KRAJEE,
                'options' => [
                    'id' => 'report-institution-select2',
                    'placeholder' => Yii::t('report', 'update.institution.placeholder'),
                    'class' => 'load-institution-note',
                    'data-url' => Url::to(['institution/note', 'id' => 'ph']),
                ],
            ]) ?>
            <div
                class="institution-note<?= $model->institution_id === null || $model->institution === null ? ' hidden' : '' ?>">
                <?= Yii::t('report', 'update.institution.comment') ?>
                <div class="institution-note-container"><?= $model->institution_id === null || $model->institution === null ? '' : $model->institution->note ?></div>
            </div>
        </div>
        <div class="col-md-5 col-sm-12 va--top hint">
            <?= Yii::t('report', 'update.institution.info', ['link' => Html::a(Yii::t('report', 'update.institution.link'), ['institution/index'])]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
        <div class="col-md-5 col-sm-12 va--top hint">
            <ul>
                <li><?= Yii::t('report', 'update.name.info_1') ?></li>
                <li><?= Yii::t('report', 'update.name.info_2') ?></li>
                <li><?= Yii::t('report', 'update.name.info_3') ?></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <?= $form->field($model, 'description')->textarea(['rows' => 8]) ?>
        </div>
        <div class="col-md-5 col-sm-12 va--top hint">
            <?= Yii::t('report', 'update.description.info') ?>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'media-list',
    ]);
    ?>
    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <?php
            $pictures = $model->getPictures();
            echo $this->render('_pictures', [
                'model' => $model,
                'pictures' => $pictures,
                'showControls' => true,
            ]);
            ?>
        </div>
        <div class="col-md-5 col-sm-12 va--top hint">
            <?= Yii::t('report', 'update.pictures.info') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <?php
            $videos = $model->getVideos();
            if (count($videos) > 0) {
                echo $this->render('_videos', [
                    'videos' => $videos,
                    'showControls' => true,
                ]);
            }
            ?>
        </div>
    </div>
    <?php Pjax::end(); ?>

    <div class="row">
        <div class="col-md-7 col-sm-12 va--top">
            <div class="row">
                <div class="col-sm-12 va--top">
                    <?= $form->field($model, 'city_id')->widget(Select2::className(), [
                        'data' => \app\models\db\City::availableCities(true, false),
                        'theme' => Select2::THEME_KRAJEE,
                        'options' => [
                            'id' => 'city-select2',
                        ],
                    ]) ?>
                </div>
                <div class="col-md-8 col-sm-12">
                    <?= $form->field($model, 'user_location')->textInput() ?>
                </div>
                <div class="col-md-4 col-sm-12">
                    <?= Html::button(Yii::t('report', 'update.location.reposition'), ['class' => 'btn btn-primary report-update']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-sm-12 va--top hint">
            <?= Yii::t('report', 'update.location.info') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <?= $form->field($model, 'latitude')->textInput(['readonly' => 'readonly']) ?>
                </div>
                <div class="col-md-4 col-sm-12">
                    <?= $form->field($model, 'longitude')->textInput(['readonly' => 'readonly']) ?>
                </div>
                <div class="col-md-4 col-sm-12">
                    <?= $form->field($model, 'zoom')->textInput(['readonly' => 'readonly']) ?>
                    <?= $form->field($model, 'post_code')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'street_name')->hiddenInput()->label(false) ?>
                    <input type="hidden" name="address" value="<?= $model->street_name ?>"/>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <div id="map" style="width: 100%; height: 300px; margin: 0; padding: 0;"></div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <label class="control-label"><?= Yii::t('admin', 'map-layers') ?></label>
            <?= Select2::widget([
                'name' => 'selected_map_layers',
                'attribute' => 'selected_map_layers',
                'value' => $model->getMapLayers(),
                'data' => MapLayer::getLayers(),
                'theme' => Select2::THEME_KRAJEE,
                'options' => [
                    'multiple' => true,
                    'id' => 'map-layer-select2',
                    'placeholder' => Yii::t('admin', 'map-layers'),
                ],
            ]); ?>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <?= $form->field($model, 'project')
                ->widget(
                    Select2::className(),
                    [
                        'data' => Report::getProjects(),
                        'theme' => Select2::THEME_KRAJEE,
                        'options' => [
                            'id' => 'projects-select2',
                        ],
                    ]
                )
                ->label(Yii::t('data', 'report.inclusion.project')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('button', 'cancel'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>

<!-- Compare Modal -->
<div class="modal fade" id="compare-modal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div id="image-delete-modal" class="modal fade" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog">
    <div id="image-delete-modal-body" class="modal-dialog"></div>
</div>

<div id="video-delete-modal" class="modal fade" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog">
    <div id="video-delete-modal-body" class="modal-dialog"></div>
</div>
