<?php
use app\components\helpers\Link;
use app\components\helpers\SVG;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
?>

<?php $authChoice = AuthChoice::begin([
    'baseAuthUrl' => [Link::AUTH_AUTH],
    'autoRender' => false,
]) ?>

<?php foreach ($authChoice->getClients() as $client): ?>
    <div class="form__row">
        <?php
        $icon = SVG::icon($client->getId(), ['class' => 'icon filter__icon']);
        $linkText = Html::tag('span', Yii::t('auth', 'log-in-with-' . $client->getId()), ['button__text']);
        $linkOptions = [
            'class' => 'button button--' . $client->getId() . ' button--large button--full',
        ];
        ?>
        <?= Html::a($icon . ' ' . $linkText, Link::to(Link::AUTH_AUTH, ['authclient' => $client->getId()]), $linkOptions) ?>
    </div>
<?php endforeach; ?>

<?php AuthChoice::end() ?>