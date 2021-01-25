<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var \app\models\db\ReportAttachment $model */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('label', 'generic.close') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= 'Kép szerkesztése' ?></h4>
    </div>
    <div class="modal-body">
        <br/><br/>
        <?= Html::img(
            $model->getAttachmentUrl() . '?' . time(),
            [
                'id' => 'img-' . $model->id,
                'class' => 'img-responsive',
                'crossOrigin' => 'anonymous',
            ]
        ) ?>
        <script>
            new Darkroom('#<?= 'img-' . $model->id ?>', {
                initialize: function() {
                    var $img = $('#<?= 'img-' . $model->id ?>');
                    var $target = $img.closest('.darkroom-container').find('.canvas-container');
                    $img.clone().show().removeAttr('id').prependTo($target);
                },

                // Plugins options
                plugins: {
                    save: {
                        callback: function() {
                            var canvas = document.createElement('canvas');
                            var source = this.darkroom.canvas.lowerCanvasEl;
                            canvas.width = source.width -2;
                            canvas.height = source.height -2;
                            var ctx = canvas.getContext("2d");
                            ctx.fillStyle = 'white';
                            ctx.fillRect(0, 0, source.width, source.height);
                            ctx.drawImage(source, -1, -1);

                            var data = {
                                img: canvas.toDataURL({
                                    format: 'jpeg'
                                }),
                                id: <?= $model->id ?>
                            };
                            $.ajax('<?= Url::to(['report/update-attachment']) ?>', {
                                data: data,
                                method: 'POST',
                                async: false,
                                success: function() {
                                    $('.modal').modal('hide');
                                    location.reload();
                                },
                                error: function() {
                                    $('.modal').modal('hide');
                                    location.reload();
                                }
                            });
                        }
                    }
                }
            });
        </script>
    </div>

    <div class="modal-footer">
    </div>
</div>
