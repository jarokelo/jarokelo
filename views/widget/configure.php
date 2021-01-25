<?php
use app\components\ActiveForm;
use app\components\widgets\Pjax;
use app\models\db\City;
use yii\helpers\Html;

/** @var array $institutions */
/** @var $model \app\models\forms\WidgetForm */
?>

<?php Pjax::begin([
    'id' => 'widget-configure-pjax-container',
    'formSelector' => '#customize-widget-form',
    'linkSelector' => false,
]);?>

<aside class="hero hero--fixed flex middle-xs">
    <div class="col-xs-12">
        <div class="hero__background hero--team"></div>
        <div class="container">
            <div class="row center-xs">
                <div class="col-xs-10 col-sm-8">
                    <h2 class="heading heading--1 hero__title"><?= Yii::t('widget', 'title'); ?></h2>
                    <p class="hero__lead">
                        <?= Yii::t('widget', 'lead'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="container widget">
    <div class="row center-xs">
        <div class="col-xs-10">
            <h2 class="widget__title"><?= Yii::t('widget', 'what_is_widget.title') ?></h2>
            <p class="widget__about"><?= Yii::t('widget', 'what_is_widget.description') ?></p>
        </div>
    </div>
    <div class="row center-xs widget__content">
        <div class="col-xs-12 col-md-4">

            <iframe class="widget__preview" name="widget-preview" src="<?= $model->getIframeUrl() ?>" frameborder="0" scrolling="1"></iframe>

            <p class="widget__comment"><?= Yii::t('widget', 'share_with_code'); ?></p>
            <div class="panel panel--info">
                <div class="panel__body" style="overflow: scroll;padding:0.3em;">
                    <code class="widget__copycode" id="copycode">
                        <?= \app\components\helpers\Html::encode('<iframe scrolling="1" src="' . $model->getIframeUrl() . '"></iframe>')?>
                    </code>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <?php $form = ActiveForm::begin([
                'id' => 'customize-widget-form',
                'options' => [
                    'data-pjax' => '1',
                ],
            ]);
            $districts = $model->city_id ? \app\models\db\District::getAll($model->city_id) : [];
            ?>

            <?= $form->field($model, 'city_id')->dropDownList(City::availableCities(true, false), ['prompt' => Yii::t('label', 'generic.all')]); ?>
            <?php if (count($districts)): ?>
            <?= $form->field($model, 'district_id')->dropDownList($districts, ['prompt' => Yii::t('label', 'generic.all')]); ?>
            <?php endif; ?>
            <?= $form->field($model, 'institution_id')->dropDownList($institutions, ['prompt' => Yii::t('label', 'generic.all')]); ?>
            <?= $form->field($model, 'report_category_id')->dropDownList(\app\models\db\ReportCategory::getList(), ['prompt' => Yii::t('label', 'generic.all')]); ?>
            <?= $form->field($model, 'status')->dropDownList(\app\models\db\Report::getPublicStatuses(), ['prompt' => Yii::t('label', 'generic.all')]); ?>
            <?= $form->field($model, 'size')->dropDownList([10 => 10, 9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1]); ?>

            <?= Html::submitButton(Yii::t('button', 'submit'), ['class' => 'button button--primary', 'style' => 'display:none;']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
