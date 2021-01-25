<?php
/* @var string $content */
?>

<?php $this->beginContent($this->findViewFile('layout')); ?>
    <div class="wrap">
        <div class="container">
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent(); ?>
