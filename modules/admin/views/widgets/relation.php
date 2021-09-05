<?php

use yii\jui\JuiAsset;
use app\components\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $formName string
 * @var $existingRelations array
 * @var $label string
 * @var $selection array
 * @var $urlName string
 */

JuiAsset::register($this);
$id = rand(10000, 99999);
$selection = [
    'Lista' => $selection,
];
$selection += [
    'Műveletek' => [
        'edit' => Yii::t('relation', '- Létrehozás -'),
    ],
];
?>
    <style>
        .item {
            position: relative;
            align-items: center;
            justify-content: center;
            color: #000;
            margin: 10px 10px 10px 0;
            cursor: move;
            padding: 15px 15px 0 15px;
            background-color: #eee;
        }

        .entity_link_container {
            display: inline-block;
        }

        .form-control.form-control_inline {
            display: inline-block;
            width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 20px;
        }

        .remove_container {
            margin-top: 10px;
            margin-right: 0;
            cursor: pointer;
            position: absolute;
            right: 3px;
        }

        .add_container {
            margin-bottom: 10px;
        }
    </style>

    <div class="wrapper_<?= $id ?>">
        <label><?= $label ?></label>

        <div class="hide template_container">
            <div class="item">
                <a class="entity_link_container" href="#" style="color: black;"><span title="Megtekintés"><i class="glyphicon glyphicon-pencil"></i></span></a>

                <?= Html::dropDownList(
                    $formName,
                    [
                        -1,
                    ],
                    $selection,
                    [
                        'class' => [
                            'form-control',
                            'form-control_inline',
                            'form-selection',
                        ],
                        'prompt' => [
                            'text' => '- Válassz -',
                            'options' => [
                                'value' => -1,
                                'selected' => 'selected',
                            ],
                        ],
                    ]
                ); ?>

                <span title="Eltávolítás" class="remove_container"><i class="glyphicon glyphicon-remove"></i></span>
            </div>
        </div>

        <div class="hide item_container sortable"></div>

        <div class="add_container full_width">
            <a href="javascript: void(0);" class="btn btn-primary">
                <i class="glyphicon glyphicon-plus"></i> <?= Yii::t('custom_form', 'Hozzáadás') ?>
            </a>
        </div>
    </div>
    <script>
        function formHandler_<?= $id ?>() {
            var WRAPPER_CLASS = '.wrapper_<?= $id ?>';
            var URL_NAME = '<?= $urlName ?>';
            var $wrapper = $(WRAPPER_CLASS);
            var RELATIONS = JSON.parse('<?= json_encode($existingRelations) ?>');
            var $itemContainer = $wrapper.find('.item_container');
            var $templateContainer = $wrapper.find('.template_container');

            function relationHandler() {
                $wrapper.find('.sortable').sortable().disableSelection();
                $wrapper.find('.add_container').on('click', function() {
                    if ($itemContainer.hasClass('hide')) {
                        $itemContainer.removeClass('hide');
                    }

                    addItem();
                });
            }

            function addItem(i, value) {
                if ($itemContainer.hasClass('hide')) {
                    $itemContainer.removeClass('hide');
                }

                $itemContainer.append($templateContainer.html());

                if (value) {
                    $($(WRAPPER_CLASS + ' .item_container select')[i])
                        .val(value)
                        .closest('.item')
                        .find('.entity_link_container')
                        .attr('href', '/admin/' + URL_NAME + '/update?id=' + value);
                }

                $($(WRAPPER_CLASS + ' .item_container select')).on('change', function() {
                    $(this)
                        .closest('.item')
                        .find('.entity_link_container')
                        .attr('href', '/admin/' + URL_NAME + '/update?id=' + $(this).val());

                    if ($(this).val() === 'edit') {
                        window.open('/admin/' + URL_NAME + '/create');
                    }
                });

                initRemoveHandler();
            }

            for (var i in RELATIONS) {
                addItem(i, RELATIONS[i].id);
            }

            function initRemoveHandler() {
                $wrapper.find('.remove_container')
                    .off('click')
                    .on('click', function() {
                        $(this).closest('.item').remove();
                    });
            }

            relationHandler();
        }
    </script>

<?php
$this->registerJs('formHandler_' . $id . '();');
