<?php
/**
 * @var \Yii\Web\View $this
 * @var array $options
 * @var int $item_width
 * @var int $item_gap
 * @var int $fix_percentage
 */

use app\assets\ProgressAsset;
use app\models\db\Progress;

ProgressAsset::register($this);
Progress::AMOUNT_REQUIRED;
$sum = (int)Progress::getAmountSum();
$percentage = 0;

if ($sum) {
    $percentage = $sum / Progress::AMOUNT_REQUIRED * 100;
}

$item_width = isset($item_width) ? $item_width : 30;
$item_gap = isset($item_gap) ? $item_gap : 15;
$percentage = isset($fix_percentage) ? $fix_percentage : $percentage;

$this->registerJs('window.progress = ' . $percentage . '; window.item_width = ' . $item_width . '; 
window.item_gap = ' . $item_gap . ';', $this::POS_BEGIN);
