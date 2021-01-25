<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="h5"><?= Yii::t('report', 'report.videos') ?></div>
<div class="report__videos gallery">
<?php
foreach ($videos as $video) {
    echo '<div class="report__thumbnail col-md-4">';
    echo Html::a(Html::img($video['imageUrl']), (isset($video['videoUrlFrame']) ? $video['videoUrlFrame'] : $video['videoUrl']), ['class' => 'lightbox']);
    if (isset($showControls) && $showControls === true) {
        echo Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']), [
            'report/delete-video',
            'id' => $video['id'],
        ], [
            'class' => 'btn-modal-content btn btn-default delete-img',
            'data-url' => Url::to(['delete-video', 'id' => $video['id']]),
            'data-target' => '#video-delete-modal-body',
            'data-modal' => '#video-delete-modal',
        ]);
    }
    echo '</div>';
}
?>
</div>
