<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "contact".
 *
 * @property integer $id
 * @property integer $institution_id
 * @property string $name
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Institution $institution
 * @property RuleContact[] $ruleContacts
 * @property Rule[] $rules
 */
class Contact extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['institution_id', 'name', 'email'], 'required'],
            [['institution_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('data', 'contact.id'),
            'institution_id' => Yii::t('data', 'contact.institution_id'),
            'name'           => Yii::t('data', 'contact.name'),
            'email'          => Yii::t('data', 'contact.email'),
            'created_at'     => Yii::t('data', 'contact.created_at'),
            'updated_at'     => Yii::t('data', 'contact.updated_at'),
        ];
    }

    /**
     * The Institution relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institution_id']);
    }

    /**
     * The RuleContacts relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleContacts()
    {
        return $this->hasMany(RuleContact::className(), ['contact_id' => 'id']);
    }

    /**
     * The Rules relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['id' => 'rule_id'])->viaTable(RuleContact::tableName(), ['contact_id' => 'id']);
    }
}
