<?php

use app\components\helpers\SVG;
use app\models\db\City;
use app\models\db\Institution;
use app\models\db\Report;
use app\models\db\ReportCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\ActiveForm;
use app\components\widgets\Pjax;

/** @var int $cityId */

Pjax::begin([
    'id' => 'rss-link-change',
    'formSelector' => '#rss-submit-form',
    'linkSelector' => false,
]);
?>

    <div class="container rss-container">
        <div class="form form--padding">
            <div class="row">
                <div class="col-xs-12">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'rss-submit-form',
                        'enableClientValidation' => true,
                        'options' => [
                            'data-pjax' => 1,
                        ],
                    ]);
                    ?>
                    <p class="heading--3">
                        <?= SVG::icon(SVG::ICON_RSS, ['class' => 'icon icon--before filter__icon']) ?>
                        <span class="middle">
                            <?= Yii::t('rss', 'filter.title'); ?>
                        </span>
                    </p>

                    <div class="row">
                        <div class="col-xs-12 col-md">
                            <?= $form->field($model, 'city')->dropDownList(City::availableCities(true, false), ['prompt' => Yii::t('label', 'generic.all'), 'class' => 'full'], ['class' => 'select select--default full']); ?>
                        </div>
                        <?php if (count($districts)) { ?>
                            <div class="col-xs-12 col-md">
                                <?= $form->field($model, 'district')->dropDownList($districts, ['prompt' => Yii::t('label', 'generic.all'), 'class' => 'full'], ['class' => 'select select--default full']) ?>
                            </div>
                        <?php } ?>
                        <div class="col-xs-12 col-md">
                            <?= $form->field($model, 'institution')->dropDownList(Institution::getList(), ['prompt' => Yii::t('label', 'generic.all'), 'class' => 'full'], ['class' => 'select select--default full']); ?>
                        </div>
                        <div class="col-xs-12 col-md">
                            <?= $form->field($model, 'category')->dropDownList(ReportCategory::getList(), ['prompt' => Yii::t('label', 'generic.all'), 'class' => 'full'], ['class' => 'select select--default full']); ?>
                        </div>
                        <div class="col-xs-12 col-md">
                            <?= $form->field($model, 'status')->dropDownList(Report::getPublicStatuses(), ['prompt' => Yii::t('label', 'generic.all'), 'class' => 'full'], ['class' => 'select select--default full']); ?>
                        </div>
                    </div>

                    <p class="text--light"><?= Yii::t('rss', 'filter.message'); ?></p>

                    <?= Html::submitButton(Yii::t('button', 'submit'), ['class' => 'button button--primary', 'style' => 'display:none;']) ?>
                    <?php
                    ActiveForm::end();
                    ?>

                    <p class="heading heading--3">
                        <?php
                            $url = Url::to(['rss/stream'], true);
                            $query = http_build_query($query);
                            if ($query) {
                                $url .= '?' . $query;
                            }
                            echo Html::a($url, $url, ['target' => '_blank', 'data-pjax' => 0]);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php Pjax::end();
