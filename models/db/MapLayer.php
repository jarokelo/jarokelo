<?php

namespace app\models\db;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "map_layer".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property string $data
 * @property double $lat
 * @property double $lng
 * @property double $zoom
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Admin $createdBy
 * @property Admin $updatedBy
 */
class MapLayer extends ActiveRecord
{
    /**
     * @var UploadedFile[]
     */
    public $files;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map_layer';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->data = gzcompress($this->data, 9);
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->data = gzuncompress($this->data);
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['files'],
                'file',
                'extensions' => 'kml, kmz, js',
                'maxFiles' => 100,
                'checkExtensionByMimeType' => false,
                'skipOnEmpty' => (bool)$this->created_at,
            ],
            [['name'], 'required'],
            [['lat', 'lng', 'zoom'], 'number'],
            [['data', 'name', 'color'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('map_layer', 'id'),
            'data' => Yii::t('map_layer', 'data'),
            'name' => Yii::t('map_layer', 'name'),
            'lat' => Yii::t('map_layer', 'lat'),
            'lng' => Yii::t('map_layer', 'lng'),
            'zoom' => Yii::t('map_layer', 'zoom'),
            'color' => Yii::t('map_layer', 'color'),
            'created_by' => Yii::t('admin', 'created_by'),
            'updated_by' => Yii::t('admin', 'updated_by'),
            'created_at' => Yii::t('admin', 'created_at'),
            'updated_at' => Yii::t('admin', 'updated_at'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Admin::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Admin::class, ['id' => 'updated_by']);
    }

    /**
     * @return array
     */
    public static function getLayers()
    {
        return array_filter(
            array_reduce(
                (array)static::find()->all(),
                function (array $carry, self $input) {
                    if (!empty($input->name)) {
                        $carry[$input->id] = str_pad($input->id, '6', '0', STR_PAD_LEFT) . ' - ' . $input->name;
                    }

                    return $carry;
                },
                []
            ),
            'strlen'
        );
    }
}
