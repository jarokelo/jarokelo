<?php
use app\models\db\CustomQuestion;
?>

<section class="type_section type_<?= CustomQuestion::TYPE_SINGLE_SELECT_DROPDOWN ?>">
    <div class="item_container sortable"></div>

    <div class="add_container full_width">
        <a href="javascript: void(0);" class="btn btn-primary">
            <i class="glyphicon glyphicon-plus"></i> <?= Yii::t('custom_form', 'Hozzáadás') ?>
        </a>
    </div>
</section>
