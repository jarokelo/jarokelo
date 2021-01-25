<?php

namespace app\components;

use app\components\helpers\Link;
use Yii;

class UserComponent extends \yii\web\User
{
    const POSTFIX_PARAM = '_a';

    /**
     * @var string[]
     */
    private $_toChangeParams = ['idParam', 'authTimeoutParam', 'absoluteAuthTimeoutParam', 'returnUrlParam'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (Yii::$app->controller->module->id === 'admin') {
            $this->identityClass = 'app\models\db\Admin';
            $this->loginUrl = ['admin/auth/login'];
            $this->enableAutoLogin = true;
            $this->setAdminParams();
        } else {
            $this->identityClass = 'app\models\db\User';
            $this->loginUrl = Link::to(Link::AUTH_LOGIN);
        }

        parent::init();
    }

    private function setAdminParams()
    {
        $this->identityCookie['name'] .= self::POSTFIX_PARAM;
        array_map([$this, 'setAdminParam'], $this->_toChangeParams);
    }

    private function setAdminParam($param)
    {
        $this->$param .= self::POSTFIX_PARAM;
    }

    public function setPosition($lat, $long)
    {
        Yii::$app->session->set('user.latitude', $lat);
        Yii::$app->session->set('user.longitude', $long);
    }

    public function getPosition()
    {
        $lat = Yii::$app->session->get('user.latitude');
        $long = Yii::$app->session->get('user.longitude');

        if ($lat !== null && $long !== null) {
            return [
                'lat' => $lat,
                'long' => $long,
            ];
        }

        return false;
    }
}
