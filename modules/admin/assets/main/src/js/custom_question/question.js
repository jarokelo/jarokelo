/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */
/* eslint-disable quotes */
/* eslint-disable no-alert */
/* eslint-disable no-lonely-if */
$(function() {
    function getSelectedType() {
        return $('#customquestion-type :selected').val();
    }

    function showSelectedQuestionData() {
        $('.type_' + getSelectedType()).css({display: 'block'});
    }

    function getLengthOfTheItem() {
        return $('.type_' + getSelectedType() + ' .item').length;
    }

    function initRemoveHandler() {
        var $container = $('.type_' + getSelectedType() + ' .remove_container');
        $container.off('click');
        $container.on('click', function() {
            var $container = $(this).closest('.item');

            // Last item can not be removed
            if (getLengthOfTheItem() > 1) {
                $container.remove();
            } else {
                $container.find('input').val('');
            }
        });
    }

    function getTemplate() {
        switch (getSelectedType()) {
            case TYPE_RADIO:
                return getRadioTemplate();
            case TYPE_CHECKBOX:
                return getCheckboxTemplate();
            case TYPE_SINGLE_SELECT_DROPDOWN:
                return getSingleSelectDropdownTemplate();
            default:
                // ..
        }

        return '';
    }

    function createInput() {
        var $container = $('.type_' + getSelectedType() + ' .item_container');
        var $input = getTemplate();
        $container.append($input);
        initRemoveHandler();
    }

    function initializeInputValues() {
        var $container = $('.type_' + getSelectedType() + ' .item_container');
        var $input = getTemplate();

        if (getSelectedType() === TYPE_LINEAR_SCALE) {
            var $answerOptions = ANSWER_OPTIONS[0];

            if ($answerOptions) {
                $('[name="CustomQuestion[answer_options][linear_scale][left_label]"]').val($answerOptions.left_label);
                $('[name="CustomQuestion[answer_options][linear_scale][right_label]"]').val($answerOptions.right_label);
                $('[name="CustomQuestion[answer_options][linear_scale][scale_left]"]').val($answerOptions.scale_left);
                $('[name="CustomQuestion[answer_options][linear_scale][scale_right]"]').val($answerOptions.scale_right);
            }
        } else {
            if (ANSWER_OPTIONS.length > 0) {
                ANSWER_OPTIONS.forEach(function(el) {
                    $container.append($input);
                    var $current = $($container.find('.item')[$container.find('.item').length - 1]);
                    var $textInput = $current.find('input');
                    $textInput.val(el.value);
                    initRemoveHandler();
                });
            }
        }
    }

    $('#customquestion-type').on('change', function() {
        $('.type_section').css({
            display: 'none'
        });
        showSelectedQuestionData();

        // Creating the input only the first time
        if (getLengthOfTheItem() === 0) {
            createInput();
        }
    });

    $('.add_container').click(function() {
        var $item = $('.type_' + getSelectedType() + ' .item');
        $($item[$item.length - 1]).find('input').focus();
    });

    $('.add_container a').click(function() {
        createInput();
    });

    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    var $form = $('#custom-question');

    $form.on('beforeSubmit', function() {
        var items = $form.serializeObject();
        var formData = new FormData();

        for (var i in items) {
            formData.append(i, items[i]);
        }

        $('.type_' + getSelectedType() + ' .item').each(function(k, v) {
            if (getSelectedType() !== TYPE_LINEAR_SCALE) {
                formData.append('CustomQuestion[answer_options][text][' + k + ']', ($(v).find('input').val() || 0));
            }
        });

        $.ajax({
            dataType: 'json',
            contentType: false,
            processData: false,
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            success: function(data) {
                if (!data) {
                    alert('Hiba történt!');
                    setTimeout(function() {
                        document.location.reload();
                    }, 3000);
                } else {
                    if (typeof data === 'object') {
                        var $errorMessage = '';

                        for (var i in data) {
                            $errorMessage += data[i][0] + "\n";
                        }
                        alert('Hiba történt! ' + $errorMessage);
                        // DO NOT RELOAD THE PAGE
                    } else {
                        document.location.href = '/admin/custom-question/index';
                    }
                }
            },
            error: function(error) {
                console.error(error);
                alert('Hiba történt!');
                setTimeout(function() {
                    document.location.reload();
                }, 3000);
            }
        });

        return false;
    });

    // Initializing sorting mechanism
    $('.sortable').sortable().disableSelection();

    initializeInputValues();
    showSelectedQuestionData();
});
