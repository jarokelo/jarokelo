<?php

use app\modules\admin\models\AnswerForm;

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var \yii\web\View $this */
/* @var \app\models\db\Contact[] $contacts */
/* @var int[] $selectedContacts */
/* @var boolean $selectIfEmpty */

if (!isset($selectedContacts) || !is_array($selectedContacts)) {
    $selectedContacts = [];
}

if (!isset($selectIfEmpty)) {
    $selectIfEmpty = false;
}

?>

<div class="contact-list">
    <div class="form-group">
    <?= Html::activeRadioList(new AnswerForm(), 'contactId', ArrayHelper::map($contacts, 'id', function ($contact) {
        /* @var \app\models\db\Contact $contact */
        return $contact->name . ' (' . $contact->email . ')';
    })) ?>
    </div>
</div>
