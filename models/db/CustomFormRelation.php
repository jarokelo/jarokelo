<?php

namespace app\models\db;

use Yii;

/**
 * This is the model class for table "custom_form_relations".
 *
 * @property int $custom_form_id
 * @property string $type
 * @property int $entity_id
 * @property int $priority
 *
 * @property CustomForm $customForm
 */
class CustomFormRelation extends \yii\db\ActiveRecord
{
    const TYPE_REPORT_CATEGORY = 'report_category';
    const TYPE_REPORT_TAXONOMY = 'report_taxonomy';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'custom_form_relations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'entity_id', 'priority'], 'required'],
            [['type'], 'string'],
            [['entity_id', 'priority'], 'integer'],
            [['custom_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomForm::className(), 'targetAttribute' => ['custom_form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'custom_form_id' => Yii::t('custom_form_relations', 'Egyedi adatlap ID'),
            'type' => Yii::t('custom_form_relations', 'Típus'),
            'entity_id' => Yii::t('custom_form_relations', 'Entitás azonosító'),
            'priority' => Yii::t('custom_form_relations', 'Prioritás'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomForm()
    {
        return $this->hasOne(CustomForm::className(), ['id' => 'custom_form_id']);
    }

    /**
     * @param int $entityId
     * @param string $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRelationsById($entityId, $type = self::TYPE_REPORT_CATEGORY)
    {
        return static::find()
            ->where(['type' => $type])
            ->andWhere(['entity_id' => $entityId])
            ->all();
    }
}
