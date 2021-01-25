<?php

namespace app\modules\admin\models;

use app\models\db\Admin;

use app\models\db\User;
use Yii;

use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * User form for creating a new User.
 *
 * @package app\models\forms
 */
class NewAdminPasswordForm extends Model
{
    const PASSWORD_REGEX_LENGTH  = '^(.){6,}$';
    const PASSWORD_REGEX_NUMBER  = '\d{1,}';
    const PASSWORD_REGEX_CAPITAL = '[A-Z]{1,}';

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $new_password;

    /**
     * @var string
     */
    public $repeat_password;

    /**
     * @var Admin
     */
    private $_user;

    public function init()
    {
        parent::init();
        $this->_user = Admin::findOne($this->userId);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_password', 'repeat_password'], 'required'],
            [['repeat_password'], 'compare', 'compareAttribute' => 'new_password'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_LENGTH . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_NUMBER . '/'],
            [['new_password'], 'match', 'pattern' => '/' . self::PASSWORD_REGEX_CAPITAL . '/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'new_password' => Yii::t('user', 'form.new_password'),
            'repeat_password' => Yii::t('user', 'form.repeat_password'),
        ]);
    }

    public function updatePassword()
    {
        if ($this->_user === null) {
            return false;
        }

        $this->_user->hashPassword($this->new_password);
        $this->_user->is_old_password = 0;
        if (!$this->_user->save()) {
            throw new Exception(VarDumper::dumpAsString($this->_user->getErrors()));
        }

        return true;
    }
}
