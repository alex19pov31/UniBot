<?php

namespace UniBot;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\ProviderInterface;

class TelegramProvider implements ProviderInterface
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
    /**
     * @var BotInterface
     */
    private $bot;

    public function __construct(string $token, string $listenUrl)
    {
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
        $this->sendRequest(
            'sendMessage',
            $options
        );

        return 0;
    }

    public function update($data)
    {
        $messageText = $data['text'];
        $chatId = $data['chat_id'];
        $messageId = $data['message_id'];
        $this->bot->update(new TelegramMessage($this, [
            'message' => $messageText,
            'chat_id' => $chatId,
            'user_id' => 0,
            'message_id' => $messageId,
        ]));
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

    public function sendMessageUser($userId, string $messageText, array $options = null): int
    {
        // TODO: Implement sendMessageUser() method.
    }

    public function setBot(BotInterface $bot)
    {
        $this->bot = $bot;
    }
}