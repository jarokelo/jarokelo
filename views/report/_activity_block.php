<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\ReportActivity $model */
/* @var \app\models\db\Report $report */

if ($model->showComment()) { ?>
    <?php \yii\widgets\Pjax::begin([
        'id' => 'page-pjax-container-' . $model->id,
        'linkSelector' => false,
        'clientOptions' => ['cache' => false],
    ]); ?>
    <li class="comment comment--default" data-id="<?= $model->id; ?>">
        <?= Html::img($model->getPictureUrl(), ['class' => 'comment__media'])?>
        <div class="comment__body">
            <p class="comment__message">
                <?= $model->getMessage(); ?>
            </p>
            <time class="comment__date"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></time>

            <div class="comment__text">
                <?= $this->render('_activity_block-comment', [
                    'model' => $model,
                ])?>
            </div>

            <?= $this->render('_activity_block-attachments', [
                'model' => $model,
            ]) ?>

            <div class="comment__actions">
                <?= $this->render('_activity-rating-form', [
                    'model' => $model,
                    'type' => \app\models\db\ReportActivityRatings::FORM_TYPE_SIDEBAR,
                ])?>
            </div>
        </div>
    </li>
    <?php \yii\widgets\Pjax::end(); ?>
<?php } else { ?>
    <li class="comment comment--activity">
        <img src="<?= Url::to($model->getPictureUrl()) ?>" alt="" class="comment__media">
        <div class="comment__body">
            <p class="comment__message">
                <?= $model->getMessage(); ?>
            </p>
            <time class="comment__date"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></time>
            <?= $this->render('_activity_block-attachments', [
                'model' => $model,
            ]) ?>
        </div>
    </li>
<?php } ?>
