<?php

namespace app\components;

/**
 * Soft Delete Trait
 *
 * @property boolean $is_deleted
 * @method boolean save()
 * @method static string tableName()
 * @method static \yii\db\Connection getDb()
 */
trait SoftDeleteTrait
{
    public static function deleteAll($condition = '', $params = [])
    {
        $command = static::getDb()->createCommand();
        $command->update(static::tableName(), ['is_deleted' => 1], $condition, $params);

        return $command->execute();
    }

    public static function find()
    {
        return parent::find()->where(['is_deleted' => 0]);
    }
}
