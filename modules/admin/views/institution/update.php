<?php

use app\models\db\Admin;
use app\models\db\City;
use app\models\db\Institution;

use kartik\select2\Select2;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\components\widgets\Pjax;

/* @var \app\models\db\Institution $model */
/* @var \yii\data\ActiveDataProvider $contactProvider */

$this->title = $model->name;
$this->params['breadcrumbs'] = [$this->title];
$this->params['breadcrumbs_homeLink'] = ['url' => ['institution/index'], 'label' => Yii::t('menu', 'institution')];

?>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <div class="block--grey">
            <h3><?= $this->title ?></h3>

            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'city_id')->widget(Select2::className(), [
                'data' => City::availableCities(),
                'theme' => Select2::THEME_KRAJEE,
            ]) ?>
            <?= $form->field($model, 'type')->widget(Select2::className(), [
                'data' => Institution::types(),
                'theme' => Select2::THEME_KRAJEE,
            ]) ?>
            <?= $form->field($model, 'note')->textarea() ?>

            <?= Html::submitButton(Yii::t('button', 'save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('button', 'cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'institution-contacts',
        'formSelector' => '#contact-edit-form',
        'options' => [
            'class' => 'pjax-hide-modal',
            'data-modal' => '#contact-add-modal',
        ],
    ]) ?>

    <div class="col-md-8">
        <?= $this->render('_contacts', [
            'dataProvider' => $contactProvider,
            'id' => $model->id,
        ]) ?>
    </div>

    <?php Pjax::end() ?>

    <?php ActiveForm::end(); ?>
</div>

<?php if (
    Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_ADD) ||
    Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)
) { ?>
    <!-- Add/Edit Modal -->
    <div class="modal fade" id="contact-add-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" id="contact-add-modal-body"></div>
    </div>
<?php } ?>

<?php if (
    Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_DELETE) ||
    Yii::$app->user->identity->hasPermission(Admin::PERM_INSTITUTION_EDIT)
) { ?>
    <!-- Delete Modal -->
    <div class="modal fade" id="contact-delete-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" id="contact-delete-modal-body"></div>
    </div>
<?php } ?>
