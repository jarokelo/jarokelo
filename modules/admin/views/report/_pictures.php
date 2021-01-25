<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="h5"><?= Yii::t('report', 'report.pictures') ?></div>
<div class="report__thumbnails gallery clearfix">

    <?php if (isset($showControls) && $showControls === true && isset($model)): ?>
        <div class="file-upload table file-upload--report dropzone text-center"
             data-upload-url="<?= Url::to(['/report/dropzone.report']) ?>"
             data-delete-url="<?= Url::to(['/report/dropzone.remove']) ?>"
             data-input-name="<?= Html::getInputName($model, 'pictures') ?>">
            <div class="dz-message">
                <span class="glyphicon glyphicon-picture"></span>
                <p class="step__final--hidden"><?= Yii::t('report', 'create.image.browse'); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php
    foreach ($pictures as $picture) {
        echo '<div class="report__thumbnail col-md-4">';
        echo Html::a(Html::img($picture['thumbnail']), $picture['url'], ['class' => 'lightbox']);

        if (isset($showControls) && $showControls === true) {
            echo Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-edit']),
                [
                    'report/edit-image',
                    'id' => $picture['id'],
                ],
                [
                    'class' => 'btn-modal-content btn btn-default edit-img',
                    'data-url' => Url::to(['edit-image', 'id' => $picture['id']]),
                    'data-target' => '#image-editor-modal-body',
                    'data-modal' => '#image-editor-modal',
                ]
            );

            echo Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']),
                [
                    'report/delete-image',
                    'id' => $picture['id'],
                ],
                [
                    'class' => 'btn-modal-content btn btn-default delete-img',
                    'data-url' => Url::to(['delete-image', 'id' => $picture['id']]),
                    'data-target' => '#image-delete-modal-body',
                    'data-modal' => '#image-delete-modal',
                ]
            );
        }

        echo '</div>';
    }
    ?>
</div>
