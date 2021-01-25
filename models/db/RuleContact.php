<?php

namespace app\models\db;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rule_contact".
 *
 * @property integer $id
 * @property integer $rule_id
 * @property integer $contact_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Rule $rule
 * @property Contact $contact
 */
class RuleContact extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule_contact';
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
            [['rule_id', 'contact_id'], 'required'],
            [['rule_id', 'contact_id', 'created_at', 'updated_at'], 'integer'],
            [['rule_id', 'contact_id'], 'unique', 'targetAttribute' => ['rule_id', 'contact_id'], 'message' => 'The combination of Rule Id and Contact Id has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('data', 'rule_contact.id'),
            'rule_id'    => Yii::t('data', 'rule_contact.rule_id'),
            'contact_id' => Yii::t('data', 'rule_contact.contact_id'),
            'created_at' => Yii::t('data', 'rule_contact.created_at'),
            'updated_at' => Yii::t('data', 'rule_contact.updated_at'),
        ];
    }

    /**
     * The Rule relations.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * The Contact relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
}
