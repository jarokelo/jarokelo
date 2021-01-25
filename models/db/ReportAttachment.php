<?php

namespace app\models\db;

use app\components\storage\StorageInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\image\drivers\Image_GD;
use app\components\helpers\S3;
use yii\web\BadRequestHttpException;
use yii\base\ErrorException;
use yii\image\drivers\Image;

/**
 * This is the model class for table "report_attachment".
 *
 * @property integer $id
 * @property integer $report_id
 * @property integer $report_activity_id
 * @property integer $email_id
 * @property integer $type
 * @property integer $status
 * @property string $url
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $storage
 *
 * @property ReportActivity $reportActivity
 * @property Email $email
 * @property Report $report
 * @property ReportAttachmentOriginal $reportAttachmentOriginal
 */
class ReportAttachment extends ActiveRecord
{
    /**
     * @var array
     */
    protected $s3Buffer = [];

    const STATUS_HIDDEN  = 0;
    const STATUS_VISIBLE = 1;

    const TYPE_PICTURE         = 0;
    const TYPE_VIDEO           = 1;
    const TYPE_ATTACHMENT      = 2;
    const TYPE_COMMENT_PICTURE = 3;

    const SIZE_PICTURE_THUMBNAIL = 1; // bc $isThumbnail true
    const SIZE_PICTURE_ORIGINAL = 0; // bc $isThumbnail false
    const SIZE_PICTURE_MEDIUM = 2;
    const SIZE_PICTURE_EDM = 3;

    const VIDEO_YOUTUBE = 'youtube';
    const VIDEO_VIMEO   = 'vimeo';
    const VIDEO_INDAVIDEO   = 'indavideo';

    const REGEX_YOUTUBE = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch)?(?:\/)?(?:\?v=)?([a-zA-Z0-9\-_]+)/';
    const REGEX_VIMEO   = '/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/';
    const REGEX_INDAVIDEO   = '/(?:https?:\/\/)?(?:www\.)?indavideo\.hu\/video\/([a-zA-Z0-9_-]+)/';

    const IMG_BASE_PATH = 'web/files/report/';

    const FOLDER_EMAIL_TEMP = 'temp';
    const FOLDER_PICTURE_THUMBNAIL = 'thumb';
    const FOLDER_PICTURE_MEDIUM = 'medium';
    const FOLDER_PICTURE_EDM = 'edm';

    /**
     * set quality for lower image sizes [0-99]
     * Never set to 100 because extra large files will be saved thanks to Darkroom.js
     */
    const IMAGE_QUALITY = 99;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report_attachment';
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
            [['type', 'name'], 'required'],
            [['report_id', 'report_activity_id', 'email_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'id'                 => Yii::t('data', 'report_attachment.id'),
            'report_id'          => Yii::t('data', 'report_attachment.report_id'),
            'report_activity_id' => Yii::t('data', 'report_attachment.report_id'),
            'email_id'           => Yii::t('data', 'report_attachment.email_id'),
            'type'               => Yii::t('data', 'report_attachment.type'),
            'status'             => Yii::t('data', 'report_attachment.status'),
            'url'                => Yii::t('data', 'report_attachment.url'),
            'name'               => Yii::t('data', 'report_attachment.name'),
            'created_at'         => Yii::t('data', 'report_attachment.created_at'),
            'updated_at'         => Yii::t('data', 'report_attachment.updated_at'),
        ];
    }

    /**
     * The Email relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(Email::className(), ['id' => 'email_id']);
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
     * The ReportActivity relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportActivity()
    {
        return $this->hasOne(ReportActivity::className(), ['id' => 'report_activity_id']);
    }

    /**
     * The ReportAttachmentOriginal relation.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportAttachmentOriginal()
    {
        return $this->hasOne(ReportAttachmentOriginal::className(), ['report_attachment_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert || $this->type === self::TYPE_VIDEO) {
            return;
        }

        $tmpName = $this->name;
        $parts = explode('.', $tmpName);
        $ext = end($parts);
        $rand = uniqid();
        $this->name = "{$this->id}-{$rand}.{$ext}";
        $fileTemp = Yii::getAlias("@runtime/upload-tmp/report/{$tmpName}");

        switch (true) {
            case !$this->save():
            case file_exists($fileTemp) === false:
                return;
        }

        $s3 = new S3();

        // Upload not image type attachments to S3
        if (!$this->isImageAttachment()) {
            $s3->upload(
                $this->getUploadPath(Report::fileUrl($this->report_id)),
                file_get_contents($fileTemp)
            );
            unlink($fileTemp);
            return;
        }

        try {
            $reportFolder = Report::fileUrl($this->report_id);

            foreach ($this->getFileConfiguration($reportFolder, $fileTemp) as $config) {
                /** @var Image_GD $image */
                $image = Yii::$app->image->load($fileTemp);
                $fileContent = $image->resize(
                    isset($config['width']) ? $config['width'] : Yii::$app->params[$config['size']]['width'],
                    isset($config['height']) ? $config['height'] : Yii::$app->params[$config['size']]['height'],
                    array_key_exists('master', $config) ? $config['master'] : Image::CROP
                )->render(null, self::IMAGE_QUALITY);
                $s3->upload(
                    isset($config['name'])
                        ? $config['name']
                        : $this->getUploadPath($reportFolder),
                    $fileContent
                );
            }

            $this->storage = StorageInterface::S3;

            if (!$this->save()) {
                throw new \Exception('Failed to save report attachment');
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            @unlink($fileTemp);
        }
    }

    /**
     * @param string $reportFolder
     * @param string $fileTemp
     * @return array
     */
    public function getFileConfiguration($reportFolder, $fileTemp)
    {
        list($newWidth, $newHeight) = $this->getNewLargeFileDimensions($fileTemp);
        return [
            [
                'size' => $size = self::FOLDER_PICTURE_THUMBNAIL,
                'name' => $this->getUploadPath($reportFolder, self::FOLDER_PICTURE_THUMBNAIL),
            ],
            [
                'size' => self::FOLDER_PICTURE_MEDIUM,
                'name' => $this->getUploadPath($reportFolder, self::FOLDER_PICTURE_MEDIUM),
            ],
            [
                'size' => self::FOLDER_PICTURE_EDM,
                'name' => $this->getUploadPath($reportFolder, self::FOLDER_PICTURE_EDM),
            ],
            [
                'width' => $newWidth,
                'height' => $newHeight,
                'master' => null,
            ],
        ];
    }

    /**
     * @param string $reportFolder
     * @param string $size
     * @return string
     */
    public function getUploadPath($reportFolder, $size = null)
    {
        $base = Yii::$app->params['aws']['s3']['rootFolder'] . "{$reportFolder}/";

        if (!$size) {
            return $base . "{$this->name}";
        }

        return $base . "{$size}/{$this->name}";
    }

    /**
     * Returns the current attachment's URL.
     *
     * @param int $size returns the correct url by SIZE_PICTURE_*
     * @param bool $useBasePath by default path is generated with a query string parameter, which is model's field value updated_at,
     * to prevent browser cache
     * @return string the current attachment's URL
     */
    public function getAttachmentUrl($size = self::SIZE_PICTURE_ORIGINAL, $useBasePath = false)
    {
        $path = self::IMG_BASE_PATH . Report::fileUrl($this->report_id);
        $folder = null;

        if ($this->report_id === null) {
            // unassigned report activity tasks
            $path = self::IMG_BASE_PATH . self::FOLDER_EMAIL_TEMP;
        } elseif ($this->isPictureAttachment()) {
            $folder = static::getPictureFolderBySize($size);
        }

        $path = implode('/', $folder !== null ? [$path, $folder] : [$path]);
        $filePath = Yii::getAlias("@app/{$path}/{$this->name}");
        $updated_at = empty($this->updated_at) ? null : $this->updated_at;

        if ($this->isStorageS3()) {
            $s3Image = S3::getPath($path, $this->name);

            if ($useBasePath) {
                return $s3Image;
            }

            return sprintf(
                '%s?%s',
                $s3Image,
                $updated_at
            );
        } elseif (file_exists($filePath) && is_file($filePath)) {
            if ($useBasePath) {
                return Yii::getAlias("@{$path}/{$this->name}");
            }

            return sprintf(
                '%s?%s',
                Yii::getAlias("@{$path}/{$this->name}"),
                $updated_at
            );
        }

        return Report::getPlaceholderImage($size);
    }

    /**
     * Returns the data (id and type) for this video attachment.
     *
     * @return array|null the video's data
     */
    public function getVideoData()
    {
        if ($this->type !== self::TYPE_VIDEO) {
            return null;
        }

        return static::extractVideoData($this->url);
    }

    /**
     * Extracts the id and the type from the video url.
     *
     * @param string $url the url
     * @return array|null the video's data
     */
    public static function extractVideoData($url)
    {
        $type = null;
        $id = null;
        $imageUrl = null;
        $videoUrl = null;
        $videoUrlFrame = null;

        /* creating youtube video image and url */
        if (preg_match(self::REGEX_YOUTUBE, $url, $matches)) {
            $type = self::VIDEO_YOUTUBE;
            $id = $matches[1];

            $imageUrl = 'http://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
            $videoUrl = 'http://www.youtube.com/embed/' . $id;
        }

        /* creating vimeo video image and url */
        if (preg_match(self::REGEX_VIMEO, $url, $matches)) {
            $type = self::VIDEO_VIMEO;
            $id = $matches[1];

            $remote_data = file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php');
            if (empty($remote_data)) {
                return null;
            }

            $imageUrl = unserialize($remote_data)[0]['thumbnail_large'];
            $videoUrl = 'https://player.vimeo.com/video/' . $id;
            $videoUrlFrame = 'https://vimeo.com/' . $id;
        }

        /* creating indavideo image and url */
        if (preg_match(self::REGEX_INDAVIDEO, $url, $matches)) {
            $type = self::VIDEO_INDAVIDEO;
            $id = $matches[1];

            $remote_data = file_get_contents('http://indavideo.hu/oembed/' . $id);

            if (empty($remote_data)) {
                return null;
            }

            $xml = new \SimpleXMLElement($remote_data);

            $imageUrl = (string)$xml->thumbnail_m_url;
            $videoUrl = 'http://indavideo.hu/player/video/' . (string)$xml->hash . '/';
        }

        if ($id !== null) {
            return [
                'hash' => md5($id),
                'id' => $id,
                'type' => $type,
                'imageUrl' => $imageUrl,
                'videoUrl' => $videoUrl,
                'videoUrlFrame' => $videoUrlFrame,
                'width' => Yii::$app->params['video']['width'],
                'height' => Yii::$app->params['video']['height'],
            ];
        }

        return null;
    }

    /**
     * Returns the current attachment's path on server.
     *
     * @param int $size
     * @param bool $getLegacyPath
     * @return string the current attachment's path on server
     */
    public function getAttachmentPath($size = self::SIZE_PICTURE_ORIGINAL, $getLegacyPath = false)
    {
        $path = self::IMG_BASE_PATH . Report::fileUrl($this->report_id);
        $folder = static::getPictureFolderBySize($size);

        if ($folder !== null) {
            $alias = Yii::getAlias("@app/{$path}/{$folder}/{$this->name}");

            if ($this->isStorageS3() && !$getLegacyPath) {
                return S3::getPathFromAbsolute($alias);
            }

            return $alias;
        }

        $alias = Yii::getAlias("@app/{$path}/{$this->name}");

        if ($this->isStorageS3() && !$getLegacyPath) {
            return S3::getPathFromAbsolute($alias);
        }

        return $alias;
    }

    /**
     * @param $postImage
     * @return array|bool
     * @throws BadRequestHttpException
     * @throws ErrorException
     */
    public function updatePicturesAfterEdit($postImage)
    {
        // separate data
        $img = explode(',', $postImage);
        $img_mime = $img[0];
        $img_b64 = $img[1];
        // get extension
        $ext = null;
        if ($img_mime == 'data:image/png;base64') {
            $ext = 'png';
        } elseif ($img_mime == 'data:image/jpeg;base64') {
            $ext = 'jpg';
        } else {
            throw new BadRequestHttpException('Bad mime.');
        }

        $oldPicturePaths = [
            self::SIZE_PICTURE_ORIGINAL  => $this->getAttachmentPath(),
            self::SIZE_PICTURE_THUMBNAIL => $this->getAttachmentPath(self::SIZE_PICTURE_THUMBNAIL),
            self::SIZE_PICTURE_MEDIUM    => $this->getAttachmentPath(self::SIZE_PICTURE_MEDIUM),
            self::SIZE_PICTURE_EDM       => $this->getAttachmentPath(self::SIZE_PICTURE_EDM),
        ];

        foreach ($oldPicturePaths as $size => $oldPicturePath) {
            // update extension
            $path = explode('.', $oldPicturePath);
            $old_ext = array_pop($path);
            array_push($path, $ext);
            $path = implode('.', $path);

            // rename file and update model if necessary
            if ($old_ext !== $ext) {
                if ($this->isStorageS3()) {
                    $this->s3Buffer[] = [
                        'rename' => [
                            'old' => $oldPicturePath,
                            'new' => $path,
                        ],
                    ];
                } else {
                    @rename($oldPicturePath, $path);
                }
            }

            $reportFolder = Report::fileUrl($this->report_id);

            if ($size == self::SIZE_PICTURE_ORIGINAL) {
                $filename = explode('/', $path);
                $filename = array_pop($filename);
                $this->name = $filename;
                $this->saveOriginalPicture();

                if (!$this->save(true, ['name', 'updated_at'])) {
                    return $this->errors;
                }
            }

            if ($size == self::SIZE_PICTURE_ORIGINAL) {
                $folderPath = Yii::getAlias("@app/web/files/report/{$reportFolder}/");
            } else {
                $folder = '';

                switch ($size) {
                    case self::SIZE_PICTURE_ORIGINAL:
                        break;
                    case self::SIZE_PICTURE_THUMBNAIL:
                        $folder = self::FOLDER_PICTURE_THUMBNAIL;
                        break;
                    case self::SIZE_PICTURE_MEDIUM:
                        $folder = self::FOLDER_PICTURE_MEDIUM;
                        break;
                    case self::SIZE_PICTURE_EDM:
                        $folder = self::FOLDER_PICTURE_EDM;
                        break;
                }

                if (!$folder) {
                    throw new \InvalidArgumentException('Invalid size');
                }

                $folderPath = Yii::getAlias("@app/web/files/report/{$reportFolder}/{$folder}/");
            }

            // save new content to the correct size
            $newPath = $this->getAttachmentPath($size, true);
            $newPathS3 = $this->getAttachmentPath($size);
            $imageContent = base64_decode($img_b64);

            if ($this->isStorageS3()) {
                if (!is_dir($folderPath)) {
                    FileHelper::createDirectory($folderPath);
                }
            }

            file_put_contents($newPath, $imageContent);

            if ($size !== self::SIZE_PICTURE_ORIGINAL) {
                /** @var Image_GD $newFile */
                $newFile = Yii::$app->image->load($newPath);
                $sizeName = static::getPictureFolderBySize($size);
                $resize = $newFile->resize(
                    Yii::$app->params[$sizeName]['width'],
                    Yii::$app->params[$sizeName]['height'],
                    Image::CROP
                );

                if ($this->isStorageS3()) {
                    $this->s3Buffer[] = [
                        'upload' => [
                            'name' => $newPathS3,
                            'content' => $resize->render(null, self::IMAGE_QUALITY),
                        ],
                    ];
                } else {
                    $resize->save($newPath, self::IMAGE_QUALITY);
                }
            } else {
                // leave the image quality, because Darkroom sends the picture without compression
                /** @var Image_GD $file */
                $file = Yii::$app->image->load($newPath);

                if ($this->isStorageS3()) {
                    $this->s3Buffer[] = [
                        'upload' => [
                            'name' => $newPathS3,
                            'content' => $file->render(null, self::IMAGE_QUALITY),
                        ],
                    ];
                } else {
                    $file->save($newPath, self::IMAGE_QUALITY);
                }
            }

            if ($this->isStorageS3()) {
                // Removing temporary file from the disk..
                @unlink($newPath);
            }
        }

        if (!$this->isStorageS3() || !$this->s3Buffer || !is_array($this->s3Buffer)) {
            return true;
        }

        $this->processBuffer();
        return true;
    }

    /**
     *
     */
    protected function processBuffer()
    {
        $s3 = new S3();
        // Processing copy later to ensure files were renamed already
        $copy = [];

        foreach ($this->s3Buffer as $buffer) {
            foreach ($buffer as $operation => $value) {
                switch ($operation) {
                    case 'rename':
                        $prepareOld = str_replace(Yii::$app->params['aws']['s3']['baseObjectUrl'], '', $value['old']);
                        $prepareNew = str_replace(Yii::$app->params['aws']['s3']['baseObjectUrl'], '', $value['new']);
                        $s3->copy($prepareOld, $prepareNew);
                        $s3->delete($value['old']);
                        break;
                    case 'copy':
                        foreach ($value as $size => $item) {
                            $copy[] = [
                                'from' => $item['from'],
                                'to' => $item['to'],
                            ];
                            // DO NOT process here S3 copy!
                        }

                        break;
                    case 'upload':
                        // Creating new image
                        $s3->upload($value['name'], $value['content']);

                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid operation');
                }
            }
        }

        foreach ($copy as $value) {
            $s3->copy($value['from'], $value['to']);
        }
    }

    /**
     *
     */
    private function saveOriginalPicture()
    {
        // Only back up the Report's pictures (comment's pictures, email attachments or videos are skipped)
        if ($this->type !== self::TYPE_PICTURE) {
            return;
        }

        if (ReportAttachmentOriginal::find()->where([
            'report_attachment_id' => $this->id,
            'report_id' => $this->report_id,
            'type' => $this->type,
        ])->exists()) {
            return;
        }

        $orig = $this->getReportOriginal();
        $basePath = self::IMG_BASE_PATH . Report::fileUrl($this->report_id);

        if ($orig->save()) {
            $originalFrom = Yii::getAlias("@app/{$basePath}/{$this->name}");
            $originalTo = Yii::getAlias("@app/{$basePath}/{$orig->name}");
            $thumbFrom = Yii::getAlias("@app/{$basePath}/" . self::FOLDER_PICTURE_THUMBNAIL . "/{$this->name}");
            $thumbTo = Yii::getAlias("@app/{$basePath}/" . self::FOLDER_PICTURE_THUMBNAIL . "/{$orig->name}");
            $mediumFrom = Yii::getAlias("@app/{$basePath}/" . self::FOLDER_PICTURE_MEDIUM . "/{$this->name}");
            $mediumTo = Yii::getAlias("@app/{$basePath}/" . self::FOLDER_PICTURE_MEDIUM . "/{$orig->name}");

            if ($this->isStorageS3()) {
                // Don't open S3 stream here!
                $this->s3Buffer[] = [
                    'copy' => [
                        'original' => [
                            'from' => S3::getPathFromAbsolute($originalFrom, true),
                            'to' => S3::getPathFromAbsolute($originalTo, true),
                        ],
                        self::FOLDER_PICTURE_MEDIUM => [
                            'from' => S3::getPathFromAbsolute($mediumFrom, true),
                            'to' => S3::getPathFromAbsolute($mediumTo, true),
                        ],
                        self::FOLDER_PICTURE_THUMBNAIL => [
                            'from' => S3::getPathFromAbsolute($thumbFrom, true),
                            'to' => S3::getPathFromAbsolute($thumbTo, true),
                        ],
                    ],
                ];
            } else {
                @copy($originalFrom, $originalTo);
                @copy($thumbFrom, $thumbTo);
                @copy($mediumFrom, $mediumTo);
            }
        }
    }

    /**
     * @return ReportAttachmentOriginal
     */
    private function getReportOriginal()
    {
        return new ReportAttachmentOriginal([
            'report_attachment_id' => $this->id,
            'report_id'            => $this->report_id,
            'type'                 => $this->type,
            'url'                  => $this->url,
            'name'                 => str_replace("{$this->id}-", "{$this->id}-original-", $this->name),
            'storage'              => StorageInterface::S3,
        ]);
    }

    public static function getPictureFolderBySize($size)
    {
        switch ($size) {
            case self::SIZE_PICTURE_EDM:
                return self::FOLDER_PICTURE_EDM;
                break;
            case self::SIZE_PICTURE_THUMBNAIL:
                return self::FOLDER_PICTURE_THUMBNAIL;
                break;
            case self::SIZE_PICTURE_MEDIUM:
                return self::FOLDER_PICTURE_MEDIUM;
                break;
            default:
                return null;
                break;
        }
    }

    public static function getTypeGuess($file_path)
    {
        if (in_array(mime_content_type(Yii::getAlias($file_path)), Yii::$app->params['allowed_mime_types']['image'])) {
            return self::TYPE_COMMENT_PICTURE;
        }

        return self::TYPE_ATTACHMENT;
    }

    private function getNewLargeFileDimensions($path)
    {
        list($width, $height) = getimagesize($path);

        $isLandscape = $width > $height;

        $maxWidth = ArrayHelper::getValue(Yii::$app->params, 'original.maxWidth');
        $maxHeight = ArrayHelper::getValue(Yii::$app->params, 'original.maxHeight');

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return [$width, $height];
        }

        return [$maxWidth, $maxHeight];
    }

    public function isPictureAttachment()
    {
        if ($this->type == self::TYPE_PICTURE || $this->type == self::TYPE_COMMENT_PICTURE) {
            return true;
        }

        return false;
    }

    public function isImageAttachment()
    {
        if ($this->isPictureAttachment()) {
            return true;
        }

        if ($this->type == self::TYPE_ATTACHMENT && is_file($this->getAttachmentPath())) {
            try {
                $info = getimagesize($this->getAttachmentPath());
            } catch (ErrorException $e) {
                $info = [];
                Yii::error('[ReportAttachment/isImageAttachment]Cannot get image size for file: ' . $this->getAttachmentPath() . "\nException:" . $e);
            }

            if (empty($info)) {
                return false;
            }

            return in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG]);
        }

        return false;
    }

    /**
     *
     */
    public function deleteFile()
    {
        if ($this->isStorageS3()) {
            $s3 = new S3();
            $s3->delete($this->getAttachmentPath());
            $s3->delete($this->getAttachmentPath(self::FOLDER_PICTURE_THUMBNAIL));
        } else {
            if (file_exists($path = $this->getAttachmentPath())) {
                @unlink($path);
            }

            if (file_exists($pathThumb = $this->getAttachmentPath(self::FOLDER_PICTURE_THUMBNAIL))) {
                @unlink($pathThumb);
            }
        }
    }

    /**
     * @return bool
     */
    public function isStorageS3()
    {
        return $this->storage == StorageInterface::S3;
    }

    /**
     *
     */
    public function afterDelete()
    {
        parent::afterDelete();

        if ($this->isStorageS3()) {
            $s3 = new S3();
            $s3->delete($this->getAttachmentPath());
            $s3->delete($this->getAttachmentPath(self::SIZE_PICTURE_THUMBNAIL));
            $s3->delete($this->getAttachmentPath(self::SIZE_PICTURE_MEDIUM));
            $s3->delete($this->getAttachmentPath(self::SIZE_PICTURE_EDM));
        }
    }

    /**
     * @param integer $reportId
     * @return static
     * @TODO [jiren] DON'T cache me! Institution send report fails upon the report image changes (delete/edit)
     */
    public static function getCoverImageByReportId($reportId)
    {
        return static::find()
            ->where([
                'report_attachment.report_id' => $reportId,
                'report_attachment.type' => self::TYPE_PICTURE,
                'report_attachment.status' => self::STATUS_VISIBLE,
            ])
            ->orderBy(['report_attachment.created_at' => SORT_ASC])
            ->one();
    }
}
