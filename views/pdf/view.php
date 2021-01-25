<?php

use app\components\helpers\Html;
use yii\helpers\Url;

/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */
/* @var \app\models\db\ReportActivity[] $comments */

?>
<?= $this->render('_header.php', ['model' => $model]); ?>

<div class="datasheet">
<table width="100%" cellpadding="0" cellspacing="2" border="0">
    <tr>
        <td><b><?=Yii::t('report', 'report.street_name') ?>:</b></td>
        <td width="130"><b><?=Yii::t('report', 'report.institution') ?>:</b></td>
        <td width="130"><b><?=Yii::t('report', 'search.status') ?>:</b></td>
        <td width="130"><b><?=Yii::t('report', 'report.date') ?>:</b></td>
    </tr>
    <tr>
        <?php list($location_city, $location_address) = explode(',', $model->user_location); ?>
        <td valign="top"><?= (isset($location_city) ? $location_city . ', ' : ''); ?> <?= ($model->district ? $model->district->name . ', ' : '') . (isset($location_address) ? $location_address : '') ?></td>
        <td valign="top"><?= $model->institution ? $model->institution->name : '-'; ?></td>
        <td valign="top"><?= Yii::t('const', 'report.status.' . $model->status) ?></td>
        <td valign="top"><?= Yii::$app->formatter->asDate($model->created_at); ?></td>
    </tr>
</table>
</div>
<br />
<h1><?= $model->name ?><br>—</h1>
<div clas="content"><?= Html::formatText($model->description, 'link--default link--info') ?></div>
<h3><?=Yii::t('report', 'report.map_from_place') ?>:</h3>
<div> <img src="https://maps.googleapis.com/maps/api/staticmap?key=<?=Yii::$app->params['google']['api_key_http']; ?>&markers=color:red|<?= $model->latitude ?>,<?= $model->longitude ?>&scale=2&zoom=<?= $model->zoom ?>&size=800x300"></div>
<?php
$mediaItems = $model->getPictures(true);
if (count($mediaItems) > 0) {
    ?>
    <h3><?=Yii::t('report', 'report.pics_from_place') ?>:</h3>
    <div class="report__media gallery">
        <?php
        foreach ($mediaItems as $item) {
            echo Html::img($item['url'], ['title' => $model->name]);
        }
        ?>
    </div>
<?php } ?>

<?= $this->render('_comment', ['comments' => $comments]) ?>
