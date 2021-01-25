<?php

namespace app\models\forms;

use app\models\db\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * User form for creating a new User.
 *
 * @package app\models\forms
 */
class UserInfoChangeForm extends User
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'last_name' => Yii::t('user', 'form.last_name'),
            'first_name' => Yii::t('user', 'form.first_name'),
            'phone_number' => Yii::t('user', 'form.phone_number'),
            'email' => Yii::t('user', 'form.email'),
        ]);
    }
}
