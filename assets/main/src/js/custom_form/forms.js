/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */
/* eslint-disable no-unused-expressions */
/* eslint-disable no-redeclare */

var $form = $('#report-create-form');
var $counter = 0;

function customFormHandler($forms, $wrap) {
    $($forms).each(function(key, val) {
        if (!val.custom_questions.length) {
            return;
        }

        val.customQuestions.forEach(function(value) {
            value.type = value.type * 1;
            var $answerOptions = JSON.parse(value.answer_options);
            var $name = val.id + '_' + value.id;
            var $propName = 'ReportForm[customForm][' + val.id + '][' + value.id + ']';
            var $id = $name;
            var $validationError = 'help-block_' + val.id + '_' + value.id;
            var $helpBlock = '<div id="' + $validationError + '" class="help-block"></div>';
            value.required = 1 * value.required;

            if (value.type === TYPE_CHECKBOX) {
                $propName = $propName + '[]';
            }

            switch (value.type) {
                case TYPE_CHECKBOX:
                case TYPE_RADIO:
                    $wrap.append('<div data-id="' + $name + '" data-type="' + value.type + '" aria-describedby="' + $validationError + '" aria-required="' + (value.required === 1 ? 'true' : 'false') +
                        '" class="wrapper_container_' + $id + ' question_container error_container type_' + value.type + '"><fieldset>' +
                        '<div class="question">' + '<legend class="label label--default"><span>' + value.question + '</span>'
                        + (value.required === 1 ? ' <span style="color: #d34545;">(*)</span>' : '')
                        + (value.description ? '<div class="description">' + value.description + '</div>' : '') + '</legend>' + '</div>'
                        + '<div class="custom-question_container"></div></fieldset></div>');
                    break;
                default:
                    $wrap.append('<div data-id="' + $name + '" data-type="' + value.type + '" aria-describedby="' + $validationError + '" aria-required="' + (value.required === 1 ? 'true' : 'false') +
                        '" class="wrapper_container_' + $id + ' question_container error_container type_' + value.type + '">' +
                        '<div class="question">' + '<label class="label label--default" for="' + $id + '"><span>' + value.question + '</span>'
                        + (value.required === 1 ? ' <span style="color: #d34545;">(*)</span>' : '')
                        + (value.description ? '<div class="description">' + value.description + '</div>' : '') + '</label>' + '</div>'
                        + '<div class="custom-question_container"></div></div>');
            }

            var $container = $($wrap.find('.question_container')[$counter]).find('.custom-question_container');

            switch (value.type) {
                case TYPE_LONG_TEXT_ANSWER:
                    var $characterLimit = $answerOptions[0] && $answerOptions[0].value ? $answerOptions[0].value : 2000;
                    var $template = '<div><div style="margin-bottom: 10px;">Maximum engedélyezett karakter: ' + $characterLimit + ' </div>' +
                        '<textarea class="question_textarea input input--default" rows="6"' +
                        ' id="' + $id + '"' +
                        ' name="' + $propName + '"' +
                        ' maxlength="' + $characterLimit + '"' +
                        ' ></textarea>' +
                        ' <div style="margin-top: 10px;">Még bevihető karakterek száma: <span id="' + $id + '_character_left">' + $characterLimit + '</span></div>';
                    ' </div>';

                    $container.append($template);
                    $container.append($helpBlock);

                    setTimeout(function() {
                        $('#' + $id).bind('keyup change', function() {
                            $('#' + $id + '_character_left').text(1 * $characterLimit - 1 * $(this).val().length);
                        });
                    }, 0);

                    break;
                case TYPE_SINGLE_SELECT_DROPDOWN:
                    var $selectContainer = $('<div class="select select--default select--full"></div>');
                    var $select = $('<select id="' + $id + '" name="' + $propName + '" class="input input--default single_select_dropdown "></select>');
                    var $icon = $('<svg class="select__icon icon"><use xlink:href="' + BUNDLE_BASE_URL + '/images/icons.svg#icon-chevron-down"></use></svg>');
                    $selectContainer.append($select);
                    $selectContainer.append($icon);

                    $answerOptions.forEach(function(answer) {
                        var $option = '<option value="' + answer.value + '">' + answer.value + '</option>';
                        $select.append($option);
                    });

                    $container.append($selectContainer);
                    $container.append($helpBlock);
                    break;
                case TYPE_RADIO:
                    $answerOptions.forEach(function(answer, key) {
                        var $id = $name + '_' + key;
                        var $template = '<div class="group-container"><div class="radio" role="radio"><input id="' + $id + '" ' +
                            'type="radio" ' +
                            'name="' + $propName + '" ' +
                            'value="' + answer.value + '"></div>' +
                            '<label class="checkbox__label" for="' + $id + '">' + answer.value + '</label></div>';
                        $container.append($template);
                        $('.custom_form_container .group-container #' + $id).radio();
                    });
                    $container.append($helpBlock);
                    break;
                case TYPE_CHECKBOX:
                    var $lastId;
                    $answerOptions.forEach(function(answer, key) {
                        var $id = $name + '_' + key;
                        $lastId = $id;
                        var $template = '<div class="group-container"><div class="checkbox" role="checkbox"><input id="' + $id + '" type="checkbox" ' +
                            'name="' + $propName + '" value="' + answer.value + '"></div>' +
                            '<label class="checkbox__label" for="' + $id + '">' + answer.value + '</label></div>';
                        $container.append($template);
                        $('.custom_form_container .group-container #' + $id).checkbox({multiSelectable: true});
                    });
                    $container.append($helpBlock);
                    break;
                case TYPE_LINEAR_SCALE:
                    $answerOptions.forEach(function(answer) {
                        var $i = 0;
                        var $length = 1 * answer.scale_right;

                        // Min 1 max 10
                        if ($length < 1) {
                            $length = 1;
                        } else if ($length > 10) {
                            $length = 10;
                        }

                        var $leftLength = 0;

                        if (1 * answer.scale_left !== 0) {
                            $i = 1;
                        } else {
                            $leftLength = 1;
                        }

                        var $labelCount = 2;
                        var $width = 100 / ($length + $labelCount + $leftLength);

                        $container.append('<div class="group-container" style="top: -30px; display: inline-block; width: ' + $width + '%"><div class="center"></div></div>');

                        for ($i; $i <= $length; $i++) {
                            var $id = $name + '_' + $i;

                            var $template = '<div class="group-container" style="display: inline-block; position: relative; top: -20px; width: ' + $width + '%">' +
                                '<div class="center"><label class="label clickable label--default" for="' + $id + '">' + $i + '</label></div>' +
                                '<div class="center"><input id="' + $id + '" ' +
                                'type="radio" ' +
                                'name="' + $propName + '" ' +
                                'value="' + $i + '">' +
                                '</div></div>';
                            $container.append($template);
                            $('.custom_form_container .group-container #' + $id).radio();
                        }

                        $container.append('<div class="group-container" style="top: -30px; display: inline-block; width: ' + $width + '%"><div class="center"></div></div>');
                        $container.append('<div style="display: flex;">' +
                            '<div class="group-container" style="float: left; width: 50%; word-wrap:break-word; display: inline-block; text-align: center">' +
                                '<div>' + answer.left_label + '</div>' +
                            '</div>' +
                            '<div class="group-container" style="float: right; padding-left: 15px; width: 50%; word-wrap:break-word; display: inline-block; text-align: center">' +
                                '<div>' + answer.right_label + '</div>' +
                            '</div></div>');
                    });

                    $container.append($helpBlock);
                    break;
                default:
                    // ..
            }

            ++$counter;
        });
    });
}

function errorHandler() {
    var $hasError = false;

    $form.find('.question_container[aria-required="true"]').each(function(key, val) {
        var $type = 1 * $(val).data('type');
        var $helpBlock = $(val).find('.help-block');
        $helpBlock.empty();

        switch ($type) {
            case TYPE_LONG_TEXT_ANSWER:
                if ($(val).find('textarea').val().length === 0) {
                    $hasError = true;
                    $helpBlock.text('Kérjük adj választ erre a kérdésre');
                }

                break;
            case TYPE_CHECKBOX:
                var $value = [];

                $('input[name="' + $(val).find('input').prop('name') + '"]:checked').each(function(i) {
                    $value[i] = $(this).val();
                });

                if ($value.length === 0) {
                    $hasError = true;
                    $helpBlock.text('Kérjük adj választ erre a kérdésre');
                }

                break;
            case TYPE_RADIO:
            case TYPE_LINEAR_SCALE:
                var $value = $('input[name="' + $(val).find('input').prop('name') + '"]:checked').val();

                if (!$value) {
                    $hasError = true;
                    $helpBlock.text('Kérjük adj választ erre a kérdésre');
                }

                break;
            default:
                // ..
        }
    });

    return $hasError;
}

function nextStepHandler() {
    $(document).on('click', '[show-step="custom_form"]', function() {
        errorHandler();
    });
}

function formHandler(response) {
    var $wrap = $('.custom_form_container');
    var $emptyMessage = $('.empty_category_message');
    var $descriptionContainer = $('.description_container');
    $emptyMessage.addClass('hide');
    $descriptionContainer.removeClass('hide');
    $wrap.empty();
    $counter = 0;

    if (response && Object.keys(response).length > 0) {
        for (var i in response) {
            customFormHandler(response[i], $wrap);
        }
    } else {
        $emptyMessage.removeClass('hide');
        $descriptionContainer.addClass('hide');
    }
}

setTimeout(function() {
    nextStepHandler();
}, 0);
