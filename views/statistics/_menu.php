<?php
use app\components\helpers\Link;

echo \yii\bootstrap\Nav::widget([
    'items' => [
        [
            'label' => 'Települések',
            'url' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES]),
            'active' => (strpos(Yii::$app->request->absoluteUrl, Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_CITIES])) !== false),
        ],
        [
            'label' => 'Illetékesek',
            'url' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_INSTITUTIONS]),
            'active' => (strpos(Yii::$app->request->absoluteUrl, Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_INSTITUTIONS])) !== false),
        ],
        [
            'label' => 'Felhasználók',
            'url' => Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_USERS]),
            'active' => (Yii::$app->request->absoluteUrl === Link::to([Link::STATISTICS, Link::POSTFIX_STATISTICS_USERS])),
        ],
    ],
    'options' => [
        'class' => 'statistics__submenu',

    ],
    'activateItems' => true,
    'activateParents' => true,
]);
