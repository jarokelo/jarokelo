<?php

use app\components\widgets\Pjax;
use app\models\db\ReportActivity;
use yii\helpers\Html;
use app\components\helpers\SVG;

/* @var $this yii\web\View */
/* @var $model \app\models\db\ReportActivity */
?>

<?php Pjax::begin([
    'id' => 'show-comment-pjax-container-' . $model->id,
    'clientOptions' => ['cache' => false],
]); ?>

<a href="#close-modal" rel="modal:close" class="close">
    <?= SVG::icon(SVG::ICON_CLOSE, ['class' => 'icon'])?>
</a>
<div class="modal--text">
    <p class="heading heading--4"><?= $model->report->name ?></p>
</div>
<div class="modal--comment">
    <div class="modal--comment__profile">
        <?= Html::img($model->getPictureUrl(), ['class' => ''])?>
    </div>
    <div class="modal--comment__heading">
        <div class="row">
            <div class="col-xs-12 col-md-9">
                <p class="modal--comment__message">
                    <?= $model->getMessage(); ?>
                </p>
                <time class="modal--comment__date"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></time>
            </div>
            <div class="col-xs center-xs end-md">
                <div class="modal--comment__rating">
                    <?= $this->render('_activity-rating-form', [
                        'model' => $model,
                        'type' => \app\models\db\ReportActivityRatings::FORM_TYPE_MODAL,
                    ])?>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="modal--comment__content">
        <?= $model->renderComment(false) ?>
    </div>
</div>
<?php Pjax::end(); ?>
