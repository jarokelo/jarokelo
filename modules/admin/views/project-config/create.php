<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\db\ProjectConfig */

$this->title = Yii::t('project_config', 'Projekt konfiguráció létrehozása');
$this->params['breadcrumbs'][] = ['label' => Yii::t('project_config', 'Projekt konfigurációk'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
