<?php

namespace app\models;

use Yii;
use app\models\db\MapLayer;
use app\models\db\Report;

/**
 * This is the model class for table "report_map_layer".
 *
 * @property integer $report_id
 * @property integer $map_layer_id
 *
 * @property MapLayer $mapLayer
 * @property Report $report
 */
class ReportMapLayer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_map_layer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_id', 'map_layer_id'], 'integer'],
            [['report_id', 'map_layer_id'], 'unique', 'targetAttribute' => ['report_id', 'map_layer_id'], 'message' => Yii::t('report_map_layer', 'err_unique')],
            [['map_layer_id'], 'exist', 'skipOnError' => true, 'targetClass' => MapLayer::className(), 'targetAttribute' => ['map_layer_id' => 'id']],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Report::className(), 'targetAttribute' => ['report_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_id' => Yii::t('report_map_layer', 'Bejelentés ID'),
            'map_layer_id' => Yii::t('report_map_layer', 'Térképréteg ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMapLayer()
    {
        return $this->hasOne(MapLayer::className(), ['id' => 'map_layer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }
}
