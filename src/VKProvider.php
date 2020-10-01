<?php


namespace UniBot;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\UserServiceInterface;

class VKProvider extends BaseProvider
{
    const BASE_URL = 'https://api.vk.com/method';
    const DEFAULT_VERSION = '5.89';

    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $confirmToken;
    /**
     * @var string
     */
    private $accessToken;

    public function __construct(
        UserServiceInterface $userService,
        string $confirmToken,
        string $accessToken, array $options = null
    )
    {
        parent::__construct($userService);
        $this->client = new Client();
        $this->confirmToken = $confirmToken;
        $this->accessToken = $accessToken;
        $this->version = $options['version'] ?? static::DEFAULT_VERSION;
    }

    protected function getUrl(string $action): string
    {
        return static::BASE_URL.'/'.$action;
    }

    protected function sendRequest(string $action, array $data = null): ResponseInterface
    {
        $data = $data ?? [];
        $data['v'] = $this->version;
        $data['access_token'] = $this->accessToken;
        $params = http_build_query($data);
        $url = $this->getUrl($action).'?'.$params;

        return $this->client->get($url);
    }

    public function sendMessage($chatId, string $messageText, array $options = null): int
    {
        $options = $options ?? [];
        $options['peer_id'] = $chatId;
        $options['message'] = $messageText;

        $this->sendRequest('messages.send', $options);
        return 0;
    }

    /**
     * ```php
     * $data = json_decode(file_get_contents('php://input'), true);
     * $vkProvider->update($data);
     * die();
     * ```
     * @param $data
     * @return mixed|void
     */
    public function update($data)
    {
        if (empty($data['type'])) {
            return;
        }

        $type = (string)$data['type'];
        if ($type === 'message_new') {
            if ($this->bot instanceof BotInterface) {
                $message = (string)$data['object'];
                $chatId = (string)$data['peer_id'];
                $messageId = (string)$data['message_id'];
                $message = new VKMessage($this, [
                    'message' => $message,
                    'chat_id' => $chatId,
                    'message_id' => $messageId
                ]);

                $this->bot->update($message);
            }

            echo 'ok';
            return;
        }

        if ($type === 'confirmation') {
            echo $this->token;
            return;
        }
    }

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function unregister()
    {
        // TODO: Implement unregister() method.
    }
}