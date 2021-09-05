<?php
use app\models\db\CustomQuestion;
?>

<section class="type_section type_<?= CustomQuestion::TYPE_LONG_TEXT_ANSWER ?>">
    <p><?= Yii::t(
        'custom_form',
        'Karakter limitáció meghatározása. Alapértelmezett maximimális limit: ' . CustomQuestion::LONG_TEXT_MAX_LENGTH . ' karakter.
        Az itt megadott érték látható lesz a felhasználói oldalon is.'
    ) ?></p>
    <div class="item_container">
        <div class="item pull-left full_width"><input min="1" value="<?= CustomQuestion::LONG_TEXT_MAX_LENGTH ?>" max="<?= CustomQuestion::LONG_TEXT_MAX_LENGTH ?>" type="number" class="form form-control form-control_inline"></div>
    </div>
</section>
