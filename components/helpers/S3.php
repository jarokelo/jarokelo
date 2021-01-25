<?php
namespace app\components\helpers;

use Yii;
use RuntimeException;
use app\models\db\Config;
use Aws\S3\S3Client;

/**
 * Stream wrapper for AWS S3
 *
 * Always instantiate when working using stream operations so the stream wrapper will be initialized!
 *
 * @link https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-stream-wrapper.html
 */
class S3
{
    const ERR_STREAM_UPLOAD = 1000;
    const ERR_STREAM_READ = 1001;
    const ERR_STREAM_READ_BYTE = 1002;
    const ERR_MISSING_CREDENTIALS = 1003;
    const ERR_STREAM_RENAME = 1004;
    const ERR_STREAM_DELETE = 1005;

    const PATH_RELATIVE = 'web/files/report/';

    const ACL_PUBLIC = 'public-read';
    const ACL_PRIVATE = 'private';
    const ACL_AUTHENTICATED = 'authenticated-read';

    /**
     * @var S3Client $client
     */
    protected $client;

    /**
     * From relative server path to absolute S3 URL
     *
     * @param string $path original source path (from)
     * @param string $imgFileName name of the file (contains extension)
     * @param bool $outputRelative
     * @return string prepared S3 path
     */
    public static function getPath($path, $imgFileName, $outputRelative = false)
    {
        $sourcePath = str_replace(
            self::PATH_RELATIVE,
            Yii::$app->params['aws']['s3']['rootFolder'],
            $path
        );

        $relativeOutput = "{$sourcePath}/{$imgFileName}";

        if ($outputRelative) {
            return $relativeOutput;
        }

        return Yii::$app->params['aws']['s3']['baseObjectUrl'] . $relativeOutput;
    }

    /**
     * From absolute server path to absolute or S3 URL
     *
     * @param string $path
     * @param bool $outputRelative
     * @return string
     */
    public static function getPathFromAbsolute($path, $outputRelative = false)
    {
        $sourcePath = preg_replace(
            '#.*report/#',
            Yii::$app->params['aws']['s3']['rootFolder'],
            $path
        );

        if ($outputRelative) {
            return $sourcePath;
        }

        return Yii::$app->params['aws']['s3']['baseObjectUrl'] . $sourcePath;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function preparePathName($path)
    {
        $name = str_replace(
            Yii::$app->params['aws']['s3']['baseObjectUrl'],
            $baseUrl = Yii::$app->params['aws']['s3']['baseUrl'],
            $path
        );

        if (strpos($name, $baseUrl) === false) {
            $name = $baseUrl . $name;
        }

        return $name;
    }

    /**
     * @param array $params
     * @return S3Client
     */
    public function createClient(array $params = [])
    {
        $configData = $this->getConfiguration();

        if (!isset($configData['credentials_key']) || !isset($configData['credentials_secret'])) {
            throw new RuntimeException(
                'Missing credentials',
                self::ERR_MISSING_CREDENTIALS
            );
        }

        $this->client = new S3Client(
            array_merge(
                [
                    'version' => Yii::$app->params['aws']['s3']['version'],
                    'region' => Yii::$app->params['aws']['s3']['region'],
                    'credentials' => [
                        'key' => $configData['credentials_key'],
                        'secret' => $configData['credentials_secret'],
                    ],
                ],
                $params
            )
        );

        // Registering AWS SDK stream wrapper..
        $this->client->registerStreamWrapper();
        return $this->client;
    }

    /**
     * @return array
     */
    protected function getConfiguration()
    {
        return array_reduce(
            Config::find()->where(
                [
                    'category' => Config::CATEGORY_AWS,
                    'key' => [
                        'credentials_key',
                        'credentials_secret',
                    ],
                ]
            )
            ->select(
                [
                    'key',
                    'value',
                ]
            )
            ->asArray()
            ->all(),
            function (array $carry, array $config) {
                $carry[$config['key']] = $config['value'];
                return $carry;
            },
            []
        );
    }

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->client)) {
            $this->client = $this->createClient($params);
        }
    }

    /**
     * @param string $name
     * @param string $file content
     * @param array $context
     * @return bool
     */
    public function upload($name, $file, array $context = [])
    {
        // Granting access to read files publicly
        $stream = fopen(
            static::preparePathName($name),
            'w',
            null,
            stream_context_create(
                array_merge(
                    [
                        's3' => [
                            'ACL' => self::ACL_PUBLIC,
                        ],
                    ],
                    $context
                )
            )
        );
        fwrite($stream, $file);
        fclose($stream);

        if ($stream === false) {
            throw new RuntimeException(
                'An error occurred while uploading a file to S3',
                self::ERR_STREAM_UPLOAD
            );
        }

        return true;
    }

    /**
     * @param string $file it should contain path and file extension
     * @return string
     */
    public function fetch($file)
    {
        $content = '';

        // Open a stream in read-only mode
        if ($stream = fopen(static::preparePathName($file), 'r')) {
            // While the stream is still open
            while (!feof($stream)) {
                // Read 1,024 bytes from the stream
                $bytes = fread($stream, 1024);

                if ($bytes === false) {
                    throw new RuntimeException(
                        'An error occurred while reading a file from S3',
                        self::ERR_STREAM_READ_BYTE
                    );
                }

                $content .= $bytes;
            }

            // Be sure to close the stream resource when you're done with it
            fclose($stream);
        }

        if ($stream === false) {
            throw new RuntimeException(
                'An error occurred while reading a file from S3',
                self::ERR_STREAM_READ
            );
        }

        return $content;
    }

    /**
     * @param string $file
     */
    public function delete($file)
    {
        if (unlink(static::preparePathName($file)) === false) {
            throw new RuntimeException(
                'Failed to delete S3 object',
                self::ERR_STREAM_DELETE
            );
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @param bool $removeUrlPrefix
     */
    public function copy($from, $to, $removeUrlPrefix = false)
    {
        if ($removeUrlPrefix) {
            $from = str_replace(Yii::$app->params['aws']['s3']['baseObjectUrl'], '', $from);
            $to = str_replace(Yii::$app->params['aws']['s3']['baseObjectUrl'], '', $to);
        }

        $this->createClient()->copy(
            Yii::$app->params['aws']['s3']['bucket'],
            $from,
            Yii::$app->params['aws']['s3']['bucket'],
            $to,
            self::ACL_PUBLIC
        );
    }
}
