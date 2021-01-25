<?php

namespace app\components;

use app\assets\AppAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends \yii\widgets\ActiveField
{
    public $options = ['class' => 'form__row'];

    public $inputOptions = ['class' => 'input input--default'];

    public $errorOptions = ['class' => 'help-block']; //tooltip tooltip--bottom

    public $labelOptions = ['class' => 'label label--default'];

    public $hintOptions = ['class' => 'hint-block'];

    private $_bundleUrl;

    public function init()
    {
        parent::init();
        $this->_bundleUrl = AppAsset::register(Yii::$app->view)->baseUrl;
    }


    public function dropDownList($items, $options = [], $wrapperOptions = [])
    {
        parent::dropDownList($items, $options);

        $useAttributes = ['xlink:href' => $this->_bundleUrl . '/images/icons.svg#icon-chevron-down'];
        $svgAttributes = ['class' => 'select__icon icon'];
        $divContent = $this->parts['{input}'] . Html::tag('svg', Html::tag('use', null, $useAttributes), $svgAttributes);
        $divAttributes = empty($wrapperOptions) ? ['class' => 'select select--default'] : $wrapperOptions;

        $this->parts['{input}'] = Html::tag('div', $divContent, $divAttributes);

        return $this;
    }
}
