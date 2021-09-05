<?php
use app\models\db\CustomQuestion;
?>

<section class="type_section type_<?= CustomQuestion::TYPE_LINEAR_SCALE ?>">
    <div class="item_container">
        <div class="pull-left full_width">
            <div class="item">
                <select name="CustomQuestion[answer_options][linear_scale][scale_left]" class="form form-control select-linear_scale">
                    <option>0</option>
                    <option>1</option>
                </select>
            </div>
            <div class="item">
                <span class="linear_scale-separator pull-left">-</span>
                <select name="CustomQuestion[answer_options][linear_scale][scale_right]" class="form form-control select-linear_scale">
                    <?php for ($i = 2; $i <= 10; $i++): ?>
                        <option><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="item">
                <input name="CustomQuestion[answer_options][linear_scale][left_label]" class="form form-control linear_scale-text-1" placeholder="<?= Yii::t('custom_form', 'Bal oldali szélsőérték címkéje (opcionális)') ?>">
            </div>
            <div class="item">
                <input name="CustomQuestion[answer_options][linear_scale][right_label]" class="form form-control linear_scale-text-2" placeholder="<?= Yii::t('custom_form', 'Jobb oldali szélsőérték címkéje (opcionális)') ?>">
            </div>
        </div>
    </div>
    </div>
</section>
