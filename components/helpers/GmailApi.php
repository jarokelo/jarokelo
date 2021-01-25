<?php

namespace app\components\helpers;

use app\models\db\Config;
use Yii;
use PhpMimeMailParser\Parser;
use Google_Client;
use Google_Service_Gmail_Profile;
use Google_Service_Gmail;
use Google_Service_Gmail_ModifyMessageRequest;

/**
 *
 */
class GmailApi
{
    const USER_ID_ME = 'me';

    /**
     * @var array required authentication rights
     */
    public static $SCOPES = [
        'email',
        'profile',
        'openid',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/gmail.modify',
    ];

    /**
     * @var string
     */
    protected $authConfig;

    /**
     * @var Google_Client
     */
    protected $client = null;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     *
     */
    public function __construct()
    {
        $params = Yii::$app->params['gmail_client'];
        $configuration = Config::find()
            ->where(
                [
                    'category' => Config::CATEGORY_GMAIL,
                    'key' => [
                        'client_id',
                        'project_id',
                        'client_secret',
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
            ->all();

        foreach ($configuration as $config) {
            $params[$config['key']] = $config['value'];
        }

        $this->authConfig = [
            'web' => $params,
        ];
        $this->redirectUri = $this->authConfig['web']['redirect_uri'];
    }

    /**
     * @param $key
     * @param null $default
     * @return string
     */
    public function getConfig($key, $default = null)
    {
        if (array_key_exists($key, $this->authConfig['web'])) {
            return $this->authConfig['web'][$key];
        }

        return $default;
    }

    /**
     * Get list of Messages in user's mailbox.
     *
     * @param int limit
     * @param callable | bool $removeUnreadFlag
     * @return mixed
     */
    public function listUnreadedMessages($limit, $removeUnreadFlag = true)
    {
        $userId = self::USER_ID_ME;
        $client = $this->getClient();
        $service = new Google_Service_Gmail($client);

        $pageToken = null;
        $messages = [];
        $optParam = [];

        do {
            $optParam['q'] = 'is:unread';

            if ($pageToken) {
                $optParam['pageToken'] = $pageToken;
            }

            $messagesResponse = $service->users_messages->listUsersMessages($userId, $optParam);

            if ($messagesResponse->getMessages()) {
                foreach ($messagesResponse->getMessages() as $m) {
                    $messages[] = $m;
                    $limit--;

                    if ($limit <= 0) {
                        break;
                    }
                }

                $pageToken = $messagesResponse->getNextPageToken();
            }
        } while ($pageToken && $limit > 0);

        $parsedMessages = [];

        foreach ($messages as $msg) {
            $raw = $service->users_messages->get(
                $userId,
                $msg->id,
                [
                    'format' => 'raw',
                ]
            );

            $raw = $raw->raw;
            $switched = str_replace(['-', '_'], ['+', '/'], $raw);
            $raw = base64_decode($switched);
            $parser = (new Parser)->setText($raw);

            $wrappedMessage = new GmailEmailWrapper($parser);
            $parsedMessages[] = $wrappedMessage;

            // set mail state to read
            $removeUnread = $removeUnreadFlag;

            if ($removeUnread) {
                if (is_callable($removeUnreadFlag)) {
                    //if callable return true then remove unread flag
                    $removeUnread = call_user_func($removeUnreadFlag, $wrappedMessage);
                }

                if ($removeUnread) {
                    $mods = new Google_Service_Gmail_ModifyMessageRequest();
                    $mods->setRemoveLabelIds(['UNREAD']);
                    $service->users_messages->modify($userId, $msg->id, $mods);
                }
            }
        }

        return $parsedMessages;
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return $authUrl;
    }

    /**
     * Fetch token
     *
     * @param string $code
     * @return mixed
     */
    public function fetchAccessToken($code)
    {
        $client = $this->getClient();
        $accessToken = $this->checkResponse($client->fetchAccessTokenWithAuthCode($code));
        $client->setAccessToken($accessToken);
        return $this->checkResponse($accessToken);
    }

    /**
     * Check the response is error
     *
     * @param $response
     * @return mixed
     */
    public function checkResponse($response)
    {
        if (array_key_exists('error', $response)) {
            throw new \Exception(join(', ', $response));
        }

        return $response;
    }

    /**
     * @return Google_Service_Gmail_Profile
     */
    public function getUserProfile()
    {
        $client = $this->getClient();
        $userId = self::USER_ID_ME;
        $service = new \Google_Service_Gmail($client);
        return $this->checkResponse($service->users->getProfile($userId));
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new \Google_Client();
            $this->client->setAuthConfig($this->authConfig);
            $this->client->setScopes(static::$SCOPES);
            $this->client->setRedirectUri($this->redirectUri);
            $this->client->setAccessType('offline');
            $this->client->setPrompt('select_account consent');
        }

        return $this->client;
    }

    /**
     * @param bool $force
     * @return mixed
     */
    public function refreshAccessToken($force = false)
    {
        $client = $this->getClient();

        if ($force || $client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $response = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $response = $this->checkResponse($response);
                $client->setAccessToken($response);
                return $response;
            }

            return false;
        }

        return true;
    }
}
