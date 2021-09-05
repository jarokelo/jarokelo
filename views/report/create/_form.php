<?php

use app\models\db\ReportCategory;
use app\models\db\ReportTaxonomy;
use app\models\db\CustomQuestion;
use app\models\db\ProjectConfig;
use app\assets\AppAsset;

/**
 * @var bool $isTaxonomyEnabled
 * @var bool $isCategoryEnabled
 * @var bool $isCustomFormEnabled
 * @var AppAsset $bundle
 */
?>
<script>
    var TEXT_CHOOSE_TAXONOMY = '<?= Yii::t('report', 'create.select_taxonomy') ?>';
    var IS_TAXONOMY_ENABLED = '<?= $isTaxonomyEnabled ?>';
    var IS_CATEGORY_ENABLED = '<?= $isCategoryEnabled ?>';
    var IS_CUSTOM_FORM_ENABLED = '<?= $isCustomFormEnabled ?>';
    var DEFAULT_REPORT_CATEGORY_ID = '<?= ReportCategory::getDefaultId() ?>';
    var DEFAULT_TAXONOMY_ID = '<?= ReportTaxonomy::getDefaultId() ?>';
    var TYPE_REPORT_TAXONOMY = '<?= ProjectConfig::KEY_REPORT_TAXONOMIES ?>';
    var TYPE_REPORT_CATEGORY = '<?= ProjectConfig::KEY_REPORT_CATEGORIES ?>';
    // Question types
    var TYPE_RADIO = <?= CustomQuestion::TYPE_RADIO_BUTTON ?>;
    var TYPE_CHECKBOX = <?= CustomQuestion::TYPE_CHECKBOX ?>;
    var TYPE_LONG_TEXT_ANSWER = <?= CustomQuestion::TYPE_LONG_TEXT_ANSWER ?>;
    var TYPE_LINEAR_SCALE = <?= CustomQuestion::TYPE_LINEAR_SCALE ?>;
    var TYPE_SINGLE_SELECT_DROPDOWN = <?= CustomQuestion::TYPE_SINGLE_SELECT_DROPDOWN ?>;

    var BUNDLE_BASE_URL = '<?= $bundle->baseUrl ?>';
</script>

<style>
    .step {
        display: none;
    }

    .report--issue .step--final .step__final--hidden {
        display: none;
    }

    .report--issue .step--final .step__final {
        display: block;
    }

    .report--issue .step__partial--hidden {
        display: none;
    }

    .hidden--mobile {
        display: none;
    }

    .report--issue .step--final .step__partial--hidden {
        display: block;
    }

    .custom_form_container {
        display: block;
    }

    .custom_form_container select {
        cursor: pointer;
    }

    .custom_form_container textarea {
        display: block;
        width: 100%;
        border: 0.05em solid #f0f0f0;
    }

    .step[step="custom_form"] .form-group.form__asset {
        border-bottom: none;
    }

    .custom-question_container {
        border-bottom: 1px solid #ddd !important;
        padding: 15px 0 25px 0;
    }

    .custom_form_container .label--default {
        margin-bottom: 0;
    }

    .custom-question_container select {
        height: 60px;
        font-size: 20px;
        color: #444;
    }

    .type_5 .group-container .label {
        margin-bottom: 10px;
    }

    .type_3 .group-container, .type_2 .group-container {
        display: flex;
    }

    .type_3 .group-container .checkbox, .type_2 .group-container .checkbox {
        float: left;
    }

    .type_3 .group-container .checkbox__label, .type_2 .group-container .checkbox__label {
        float: right;
        max-width: 790px;
        word-wrap: break-word;
        padding-top: 0;
        position: relative;
        top: -6px;
    }

    .type_4 .select select {
        width: 99%;
        text-overflow: ellipsis;
        word-break: break-word;
    }

    .type_5 .custom-question_container .group-container {
        position: relative;
    }

    .type_5 .custom-question_container {
        border: none;
    }

    .custom_form_container .question_container {
        margin-top: 15px;
    }

    .center {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hide {
        display: none;
    }

    .custom_form_container .description {
        margin-top: 15px;
        text-transform: none;
        font-size: 14px;
    }

    .custom_form_container .group-container {
        margin-top: 10px;
    }

    .custom_form_container .clickable {
        cursor: pointer;
    }

    .description_container {
        font-weight: 300;
    }

    .custom_form_container .group-container .radio,
    .custom_form_container .group-container .checkbox {
        width: 25px;
        height: 25px;
    }
</style>
