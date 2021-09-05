<?php

namespace app\models\db;

use Yii;

/**
 * This is the model class for table "project_config".
 *
 * @property string $key
 * @property int $value
 */
class ProjectConfig extends \yii\db\ActiveRecord
{
    protected static $config = [];

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    const KEY_REPORT_TAXONOMIES = 'report_taxonomy';
    const KEY_REPORT_CATEGORIES = 'report_category';
    const KEY_CUSTOM_FORMS = 'custom_forms';
    const KEY_REPORT_FORM_NAME = 'report_form_name';
    const KEY_REPORT_FORM_DESCRIPTION = 'report_form_description';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_config';
    }

    /**
     * @return array
     */
    public static function getFilterKeys()
    {
        return [
            self::KEY_REPORT_CATEGORIES => Yii::t('project_config', 'Bejelentés kategóriák'),
            self::KEY_REPORT_TAXONOMIES => Yii::t('project_config', 'Bejelentés alkategóriák'),
            self::KEY_CUSTOM_FORMS => Yii::t('project_config', 'Egyedi adatlapok'),
            self::KEY_REPORT_FORM_NAME => Yii::t('project_config', 'Bejelentés tárgya'),
            self::KEY_REPORT_FORM_DESCRIPTION => Yii::t('project_config', 'Bejelentés leírása'),
        ];
    }

    /**
     * @return array
     */
    public static function getFilterValues()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('project_config', 'Engedélyezve'),
            self::STATUS_INACTIVE => Yii::t('project_config', 'Tiltva'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'string', 'max' => 255],
            [['value'], 'integer'],
            [['key'], 'unique'],
            [['key'], 'keyRelationValidator'],
        ];
    }

    public function keyRelationValidator($attribute, $params)
    {
        $msg = Yii::t('report_category', 'A "kategória" vagy az "alkategória" engedélyezve kell legyen');

        if ($this->{$attribute} == self::KEY_REPORT_CATEGORIES && $this->value == 0) {
            if (!static::isItemAllowed(self::KEY_REPORT_TAXONOMIES)) {
                $this->addError($attribute, $msg);
                return false;
            }
        } elseif ($this->{$attribute} == self::KEY_REPORT_TAXONOMIES && $this->value == 0) {
            if (!static::isItemAllowed(self::KEY_REPORT_CATEGORIES)) {
                $this->addError($attribute, $msg);
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function isItemAllowed($key)
    {
        /** @var static $entity */
        $entity = static::find()
            ->where(compact('key'))
            ->one();

        if (!$entity) {
            return true;
        }

        return $entity->value == self::STATUS_ACTIVE;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => Yii::t('project_config', 'Kulcs'),
            'value' => Yii::t('project_config', 'Érték'),
        ];
    }
}
