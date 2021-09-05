<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\ProjectConfig */

$this->title = Yii::t('project_config', 'Projekt konfiguráció módosítása', [
    'name' => $model->key,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('project_config', 'Projekt konfigurációk'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('project_config', 'Módosítás');
?>
<div class="project-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
