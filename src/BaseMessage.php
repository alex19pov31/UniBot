<?php


namespace UniBot;


use UniBot\Interfaces\MessageInterface;
use UniBot\Interfaces\ProviderInterface;

abstract class BaseMessage implements MessageInterface
{
    /**
     * @var ProviderInterface
     */
    protected $provider;
    /**
     * @var mixed
     */
    protected $messageText;
    /**
     * @var mixed
     */
    protected $chatId;
    /**
     * @var mixed
     */
    protected $userId;
    /**
     * @var mixed
     */
    protected $messageId;

    public function __construct(ProviderInterface $provider, array $options = [])
    {
        $this->provider = $provider;
        $this->messageText = (string)$options['message'];
        $this->chatId = $options['chat_id'];
        $this->userId = $options['user_id'];
        $this->messageId = $options['message_id'];
    }

    public function answer(string $message, array $options = null): int
    {
        $this->provider->sendMessage($this->chatId, $message, $options);
    }

    abstract public function reply(string $message, array $options = null): int;

    public function getMessageText(): string
    {
        return (string)$this->messageText;
    }

    public function isCommand(): bool
    {
        return strpos($this->messageText, '/') === 0;
    }
}