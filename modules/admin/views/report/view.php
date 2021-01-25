<?php

use app\components\helpers\Html;
use app\models\db\Admin;
use app\models\db\Report;
use yii\widgets\Breadcrumbs;
use app\assets\MapLayerAsset;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */

$this->title = $model->name;
// set reportId and adminId hidden fields for the websocket locker script
echo Html::hiddenInput('reportId', $model->id, ['id' => 'reportId']);
echo Html::hiddenInput('adminId', Yii::$app->user->identity->id, ['id' => 'adminId']);
$this->registerJs("admin.publicUrl = '" . $model->getUrl() . "';");

if ($mapLayers = $model->getMapLayers()):
    MapLayerAsset::register($this);
endif; ?>

<div class="row report-full">
    <div class="col-md-7 col-sm-12">
        <?= Breadcrumbs::widget([
            'links' => [$this->title],
            'homeLink' => [
                'url' => ['report/index'],
                'label' => Yii::t('menu', 'report'),
            ],
            'options' => [
                'class' => 'site-breadcrumb',
            ],
        ]) ?>

        <div class="panel panel-default report">
            <div class="panel-heading">
                <?= Html::a($model->getUniqueName(), $model->getUrl(), [
                    'id' => 'report-unique-name-link',
                    'target' => '_blank',
                ]) ?>
                <span class="label label-default label-status label-status--<?= Yii::t('const', 'report.class.' . $model->status); ?>">
                    <?= Yii::t('const', 'report.status.' . $model->status) ?>
                </span>
                <h3><?= $model->name ?></h3>
            </div>

            <div class="panel-body">

                <div class="h5"><?= Yii::t('report', 'report.institution') ?></div>
                <div><?= ($model->institution_id === null || $model->institution === null) ? '-' : $model->institution->name ?></div>
                <br/>

                <div class="h5"><?= Yii::t('report', 'report.category') ?></div>
                <div><?= $model->reportCategory->name ?></div>
                <br/>

                <div class="h5"><?= Yii::t('report', 'report.description') ?></div>
                <div><?= Html::formatText($model->description) ?></div>
                <br/>

                <div class="h5"><?= Yii::t('report', 'report.location') ?></div>
                <div><?= $model->user_location ?></div>
                <br>
                <?= $this->render('@app/views/_snippets/_mapbox', [
                    'options' => [
                        'zoom' => $model->zoom,
                        'selectors' => [
                            'map' => '#map',
                        ],
                        'center' => [
                            'lat' => Report::formatCoordinate($model->latitude),
                            'lng' => Report::formatCoordinate($model->longitude),
                        ],
                        'isAdmin' => true,
                    ] + compact('mapLayers'),
                ]);
                ?>
                <div id="map" class="report__map"></div>


                <?php
                $pictures = $model->getPictures();
                if (count($pictures) > 0) {
                    echo $this->render('_pictures', [
                        'pictures' => $pictures,
                    ]);
                }

                $videos = $model->getVideos();
                if (count($videos) > 0) {
                    echo $this->render('_videos', [
                        'videos' => $videos,
                    ]);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="report__activity report__activity--sidebar col-md-5 col-sm-10 col-sm-offset-1 col-md-offset-0">
        <?= $this->render('_activity_list', ['model' => $model]) ?>
    </div>
</div>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT) || (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_DELETE) && $model->status != Report::STATUS_DELETED)): ?>
    <div class="sticky-footer">
        <div class="container">
            <div class="row">
                <?= $this->render('_control_bar', [
                    'report' => $model,
                ]); ?>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_EDIT)): ?>
    <!-- Status Change Modal -->
    <div class="modal fade" id="status-change-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="status-change-modal-body"></div>
    </div>

    <!-- Highlight Modal -->
    <div class="modal fade" id="highlight-change-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="highlight-modal-body"></div>
    </div>

    <!-- Send Modal -->
    <div class="modal fade" id="send-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="send-modal-body"></div>
    </div>

    <!-- Answer Modal -->
    <div class="modal fade" id="answer-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="answer-modal-body"></div>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->identity->hasPermission(Admin::PERM_REPORT_DELETE)): ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="report-delete-modal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" id="report-delete-modal-body"></div>
    </div>
<?php endif ?>
