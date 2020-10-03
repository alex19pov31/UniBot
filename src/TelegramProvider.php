<?php

namespace UniBot;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use UniBot\Events\MessageEvent;
use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\UserServiceInterface;

class TelegramProvider extends BaseProvider
{
    const BASE_URL = 'https://api.telegram.org/bot';
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $listenUrl;
    /**
     * @var Client
     */
    private $client;

    public function __construct(UserServiceInterface $userService, string $token, string $listenUrl)
    {
        parent::__construct($userService);
        $this->token = $token;
        $this->listenUrl = $listenUrl;
        $this->client = new Client();
    }

    protected function getUrl(string $action): string
    {
        return static::BASE_URL.$this->token.'/'.$action;
    }

    protected function sendRequest(string $action, array $data = null): ResponseInterface
    {
        return $this->client->post(
            $this->getUrl($action),
            [
                'json' => $data
            ]
        );
    }

    public function sendMessage($chatId, string $messageText, array $options = null): int
    {
        $options = $options ?? [];
        $options['chat_id'] = $chatId;
        $options['text'] = $messageText;
        $resp = $this->sendRequest(
            'sendMessage',
            $options
        );
        $body = $resp->getBody();
        $data = json_decode($body, true);
        $result = $data['result'];
        if (!$result) {
            return 0;
        }

        return (int)$result['message_id'];
    }

    /**
     * ```php
     *  $data = json_decode(file_get_contents('php://input'), true);
     *  $telegramProvider->update($data);
     *  die();
     * ```
     * @param $data
     * @return mixed|void
     */
    public function update($data)
    {
        $messageText = $data['text'];
        $chatId = $data['chat_id'];
        $messageId = $data['message_id'];

        $message = new TelegramMessage($this, [
            'message' => $messageText,
            'chat_id' => $chatId,
            'user_id' => 0,
            'message_id' => $messageId,
        ]);

        if ($this->bot instanceof BotInterface) {
            $event = new MessageEvent($data, $chatId, $message);
            $this->bot->update($event);
        }
    }

    public function register()
    {
        $this->sendRequest(
            'setWebhook',
            [
                'url' => $this->listenUrl
            ]
        );
    }

    public function unregister()
    {
        $this->sendRequest('deleteWebhook');
    }
}