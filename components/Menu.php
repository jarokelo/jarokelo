<?php

namespace app\components;

use Yii;
use app\components\helpers\Link;

class Menu extends \yii\widgets\Menu
{
    public $options = ['class' => 'navigation__list'];
    public $linkTemplate = '<a class="navigation__link" href="{url}">{label}</a>';
    public $encodeLabels = false;
    public $activateParents = true;

    public function init()
    {
        parent::init();

        $this->setSubMenu();
    }

    /**
     * Overwrites the $submenuTemplate variable with our custom submenu.
     */
    private function setSubMenu()
    {
        $title = Yii::t('menu', 'dropdown.title');
        $lead = Yii::t('menu', 'dropdown.lead');
        $getToKnowUs = Yii::t('menu', 'get_to_know_us');
        $getToKnowUsLink = Link::to([Link::ABOUT, Link::POSTFIX_ABOUT_HOWITWORKS]);

        $this->submenuTemplate = <<< HTML
<div class="dropdown-content">
        <div class="dropdown__body">
            <strong class="dropdown__title">{$title}</strong>
            <p class="dropdown__lead">{$lead}</p>
            <a href="{$getToKnowUsLink}" class="dropdown__link">{$getToKnowUs}</a>
        </div>
        <ul class="dropdown-menu">
            {items}
        </ul>
    </div>
HTML;
    }
}
