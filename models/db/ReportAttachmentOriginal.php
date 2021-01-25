<?php

namespace app\models\db;

use app\components\helpers\S3;
use app\components\storage\StorageInterface;
use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_attachment_original".
 *
 * @property integer $report_attachment_id
 * @property integer $report_id
 * @property integer $type
 * @property string $url
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $storage
 *
 * @property Report $report
 * @property ReportAttachment $reportAttachment
 */
class ReportAttachmentOriginal extends ActiveRecord
{
    const SIZE_PICTURE_THUMBNAIL = 1; // bc $isThumbnail true
    const SIZE_PICTURE_ORIGINAL = 0; // bc $isThumbnail false
    const SIZE_PICTURE_MEDIUM = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_attachment_original';
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
            [['report_attachment_id', 'type', 'name'], 'required'],
            [['report_attachment_id', 'report_id', 'type', 'created_at', 'updated_at'], 'integer'],
            [['url'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_attachment_id' => Yii::t('data', 'report_attachment_original.report_attachment_id'),
            'report_id'            => Yii::t('data', 'report_attachment_original.report_id'),
            'type'                 => Yii::t('data', 'report_attachment_original.type'),
            'url'                  => Yii::t('data', 'report_attachment_original.url'),
            'name'                 => Yii::t('data', 'report_attachment_original.name'),
            'created_at'           => Yii::t('data', 'report_attachment_original.created_at'),
            'updated_at'           => Yii::t('data', 'report_attachment_original.updated_at'),
        ];
    }

    /**
     * @return bool
     */
    public function isStorageS3()
    {
        return $this->storage == StorageInterface::S3;
    }

    /**
     * The Report relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }

    /**
     * The ReportAttachment relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachment()
    {
        return $this->hasOne(ReportAttachment::className(), ['id' => 'report_attachment_id']);
    }

    /**
     * Returns the current attachment's URL.
     *
     * @param int $size returns the correct url by SIZE_PICTURE_*
     * @return string the current attachment's URL
     */
    public function getAttachmentUrl($size = self::SIZE_PICTURE_ORIGINAL)
    {
        $path = ReportAttachment::IMG_BASE_PATH . Report::fileUrl($this->report_id);
        $path = implode('/', [$path, ReportAttachment::getPictureFolderBySize($size)]);
        $imgPath = Yii::getAlias("@app/{$path}/{$this->name}");
        $alias = Yii::getAlias("@{$path}/{$this->name}");

        if ($this->isStorageS3()) {
            return S3::getPathFromAbsolute($alias);
        }

        if (file_exists($imgPath) && is_file($imgPath)) {
            return Yii::getAlias("@{$path}/{$this->name}");
        }

        return Report::getPlaceholderImage($size);
    }
}
