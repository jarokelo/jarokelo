<?php
use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\helpers\Html;

/** @var $model \app\models\db\ReportActivity */

echo $model->renderComment();

if ($model->showMoreButton()) {
    $baseOptions = [
        'class' => 'comment__more',
        'data-id' => $model->id,
    ];
    $overlayOptions = $model->showOverlay() ? [
        'class' => 'comment__more ajax-modal init-loader',
        'data-url' => Link::to([Link::REPORTS, 'komment', $model->id]),
    ] : [];

    echo Html::button(
        Yii::t('report', 'activity.show-more') . SVG::icon(SVG::ICON_CHEVRON_DOWN),
        array_merge($baseOptions, $overlayOptions)
    );
}
