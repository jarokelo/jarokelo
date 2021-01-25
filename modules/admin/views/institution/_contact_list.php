<?php

use yii\bootstrap\Html;

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
    <?php foreach ($contacts as $contact) { ?>
        <div class="form-group">
            <div class="checkbox">
                <label for="rule-contact-<?= $contact->id ?>">
                <?= Html::checkbox(
                    'RuleContact[' . $contact->id . ']',
                    (empty($selectedContacts) && $selectIfEmpty) || in_array($contact->id, $selectedContacts),
                    ['id' => 'rule-contact-' . $contact->id]
                ) ?>
                <?= $contact->name ?> &lt;<?= $contact->email?>&gt;</label>
            </div>
        </div>
    <?php } ?>
</div>
