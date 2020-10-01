<?php


namespace UniBot;


use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\ProviderInterface;
use UniBot\Interfaces\UserServiceInterface;

abstract class BaseProvider implements ProviderInterface
{

    /**
     * @var UserServiceInterface
     */
    protected $userService;
    /**
     * @var BotInterface
     */
    protected $bot;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    abstract public function sendMessage($chatId, string $messageText, array $options = null): int;

    abstract public function update($data);

    abstract public function register();

    abstract public function unregister();

    public function sendMessageUser($userId, string $messageText, array $options = null): int
    {
        $chatId = $this->userService->resolveChatIdByProvider($userId, $this);
        return $this->sendMessage($chatId, $messageText, $options);
    }

    public function setBot(BotInterface $bot)
    {
        $this->bot = $bot;
    }

    public function setUserService(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
}