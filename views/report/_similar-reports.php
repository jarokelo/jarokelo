<?php
/** @var $model Report */
/** @var $this \yii\web\View */

use app\models\db\Report;

?>

<section class="section container report__similar">
    <div class="section__header">
        <h2 class="heading heading--4"><?= Yii::t('report', 'similar-reports.title') ?></h2>
    </div>
    <ul class="list list--cards row">
        <?php foreach ($model->similarReports(3) as $report): ?>
            <li class="flex-eq-height col-xs-12 col-md-6 col-lg-4">
            <?= $this->render('_card', [
                'report' => $report,
            ]) ?>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
