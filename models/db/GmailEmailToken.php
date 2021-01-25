<?php

namespace app\models\db;

use app\components\helpers\GmailApi;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "gmail_email_token".
 *
 * @property string $email
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $token
 */
class GmailEmailToken extends ActiveRecord
{
    /**
     * @var array
     */
    public static $REQUIRED_TOKEN_FIELDS = [
        'email',
        'access_token',
        'expires_in',
        'scope',
        'token_type',
        'created',
        'refresh_token',
    ];

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
    public static function tableName()
    {
        return 'gmail_email_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'email'], 'required'],
            [['token'], 'string', 'max' => 4096],
            [['email'], 'email'],
        ];
    }

    /**
     * @return mixed
     */
    public function getTokenData()
    {
        return Json::decode($this->token, true);
    }

    /**
     * @param GmailApi $client
     */
    public function applyTokenToClient($client)
    {
        $client->getClient()->setAccessToken($this->getTokenData());
        $refreshResponse = $client->refreshAccessToken();

        if (is_array($refreshResponse)) {
            $this->token = Json::encode($refreshResponse);

            if (!$this->save(false)) {
                throw new \Exception('Failed to save token data');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => Yii::t('data', 'gmail.email'),
            'created_at' => Yii::t('data', 'gmail.created_at'),
            'updated_at' => Yii::t('data', 'gmail.updated_at'),
            'token'      => Yii::t('data', 'gmail.token'),
        ];
    }

    /**
     * save token data to  db.
     * @param array $tokenData
     * @return $this
     * @throws \Exception
     */
    public static function saveByToken($tokenData)
    {
        if (empty($tokenData) || !is_array($tokenData)) {
            throw new \Exception('Invalid token data!');
        }

        foreach (static::$REQUIRED_TOKEN_FIELDS as $field) {
            if (!array_key_exists($field, $tokenData)) {
                throw new \Exception('Emtpy field value: ' . $field);
            }
        }

        $email = $tokenData['email'];
        $instance = static::findOne(['email' => $email]);

        if (!$instance) {
            $instance = new static([
                'email' => $email,
            ]);
        }

        $instance->token = Json::encode($tokenData);

        if (!$instance->save()) {
            $errors = implode(PHP_EOL, $instance->getFirstErrors());
            throw new \Exception('Failed to store token data!' . PHP_EOL . $errors);
        }

        return $instance;
    }
}
