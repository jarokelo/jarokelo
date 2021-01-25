<?php
/** @var $model \app\models\db\ReportActivity */
/** @var $type string */

use app\components\ActiveForm;
use app\components\helpers\SVG;
use app\models\db\ReportActivityRatings;
use yii\helpers\Html;

ActiveForm::begin([
    'id' => 'report-rating-form-' . $model->id,
    'action' => ['report/rating', 'id' => $model->id, 'type' => $type],
    'options' => [
        'data-pjax' => '1',
    ],
]);
echo Html::hiddenInput('report', $model->report->id);
?>
    <button type="submit" name="state" value="1" class="comment__actions__button comment__actions__button--like" <?= Yii::$app->user->isGuest ? 'disabled="disabled"' : '' ?>>
        <?= SVG::icon(SVG::ICON_LIKE)?>
        <?= ReportActivityRatings::getRatings($model->id, 1); ?>
    </button>
    <button type="submit" name="state" value="0" class="comment__actions__button comment__actions__button--dislike" <?= Yii::$app->user->isGuest ? 'disabled="disabled"' : '' ?>>
        <?= SVG::icon(SVG::ICON_DISLIKE)?>
        <?= ReportActivityRatings::getRatings($model->id, 0); ?>
    </button>
<?php ActiveForm::end();
