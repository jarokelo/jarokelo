<?php

namespace app\components;

use app\assets\AppAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class LinkPager extends \yii\widgets\LinkPager
{

    public static function widget($config = [])
    {
        $bundleUrl = AppAsset::register(Yii::$app->view)->baseUrl;

        $svgAttributes = ['class' => 'icon'];
        $useAttributesPrev = ['xlink:href' => $bundleUrl . '/images/icons.svg#icon-chevron-left'];
        $useAttributesNext = ['xlink:href' => $bundleUrl . '/images/icons.svg#icon-chevron-right'];

        $iconPrev = Html::tag('svg', Html::tag('use', null, $useAttributesPrev), $svgAttributes);
        $iconNext = Html::tag('svg', Html::tag('use', null, $useAttributesNext), $svgAttributes);

        $config = ArrayHelper::merge([
            'firstPageLabel' => Yii::t('app', 'pagination.link.first'),
            'lastPageLabel' => Yii::t('app', 'pagination.link.last'),
            'prevPageLabel' => $iconPrev . Yii::t('app', 'pagination.link.prev'),
            'nextPageLabel' => Yii::t('app', 'pagination.link.next') . $iconNext,
            'maxButtonCount' => 5,

            // Customzing options for pager container tag
            'options' => [
                'tag' => 'div',
                'class' => 'pager-wrapper pagination__list',
                'id' => 'pager-container',
            ],

            // Customzing CSS class for pager link
            'linkOptions' => ['class' => 'pagination__link', 'data-pjax' => 0],
            'activePageCssClass' => 'pagination__list__item--active',
            'disabledPageCssClass' => 'pagination__link--disabled',

            // Customzing CSS class for navigating link
            'pageCssClass' => 'pagination__list__item pagination__list__number',
            'firstPageCssClass' => 'pagination__list__item pagination__pager pagination__pager--previous',
            'lastPageCssClass' => 'pagination__list__item pagination__pager pagination__pager--next',
            'prevPageCssClass' => 'pagination__list__item pagination__pager pagination__pager--previous',
            'nextPageCssClass' => 'pagination__list__item pagination__pager pagination__pager--next',
        ], $config);

        return parent::widget($config);
    }
}
