/* eslint-disable no-undef */
/* eslint-disable no-unused-expressions */
/* eslint-disable no-alert */
(function() {
    if (!IS_CUSTOM_FORM_ENABLED) {
        return;
    }

    var $reportCategoryDropDown = $('#reportform-report_category_id');
    var $taxonomyDropDown = $('#reportform-reporttaxonomyid');
    var $selectedItem;

    function handleTaxonomy() {
        if (!IS_TAXONOMY_ENABLED) {
            return;
        }

        function handleFetch(val) {
            $.get('/report-category/get-taxonomy-by-category-id', {report_category: 1 * val}, function(response) {
                if (response && Object.keys(response).length > 0) {
                    for (var i in response) {
                        $taxonomyDropDown.append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                    }
                }
            });
        }

        if (IS_CATEGORY_ENABLED) {
            // Initial value
            handleFetch($reportCategoryDropDown.val());

            $reportCategoryDropDown.on('change', function() {
                $taxonomyDropDown.html('<option value="">' + TEXT_CHOOSE_TAXONOMY + '</option>');
                handleFetch($(this).val());
            });
        } else {
            // Report category is OFF, but the taxonomy is ON
            handleFetch(DEFAULT_REPORT_CATEGORY_ID);
        }
    }

    $('[show-step="custom_form"]').on('click', function() {
        var $entityId;
        var $type;

        if (IS_TAXONOMY_ENABLED) {
            $entityId = $taxonomyDropDown.val();
            $type = TYPE_REPORT_TAXONOMY;
        } else if (IS_CATEGORY_ENABLED) {
            $entityId = $reportCategoryDropDown.val();
            $type = TYPE_REPORT_CATEGORY;
        }

        var $emptyMessage = $('.empty_category_message');
        var $descriptionContainer = $('.description_container');
        var $container = $('.custom_form_container');

        if ($entityId) {
            if ($container.text().length > 0) {
                $emptyMessage.addClass('hide');
                $descriptionContainer.removeClass('hide');
            } else {
                $emptyMessage.removeClass('hide');
                $descriptionContainer.addClass('hide');
            }
        } else {
            $emptyMessage.removeClass('hide');
            $descriptionContainer.addClass('hide');
            $container.empty();
            return;
        }

        // Ensuring the proper type of the entity
        $entityId = 1 * $entityId;

        // We don't want to load the same item multiple times
        if (!$selectedItem || ($selectedItem !== $entityId)) {
            $selectedItem = $entityId;

            $.get('/custom-form/get-custom-form-by-relation', {
                type: $type,
                entity_id: $selectedItem
            }, function(response) {
                formHandler(response);
            });
        }
    });

    handleTaxonomy();

    $(document).ready(function() {
        var $form = $('#report-create-form');
        $form.on('beforeValidate', function() {
            if (errorHandler()) {
                alert('Hiba történt! Kérjük ellenőrizd az egyedi űrlapon adott válaszaid majd küldd be ismét!');
                return false;
            }

            return true;
        });
    });
})();
