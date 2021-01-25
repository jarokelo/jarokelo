<?php

namespace app\modules\admin\models;

use Yii;

use yii\base\Model;

/**
 * Extra contact holder for send to authority functionality.
 *
 * @package app\modules\admin\models
 */
class ExtraContact extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $test;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'name', 'test'], 'required'],
            [['email'], 'email'],
            [['name'], 'string'],
            [['test'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('report', 'send.extra_contact'),
        ];
    }
}
