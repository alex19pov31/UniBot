<?php


namespace UniBot\Interfaces;


interface ProviderInterface
{
    /**
     * @param $chatId
     * @param string $messageText
     * @param array|null $options
     * @return int
     */
    public function sendMessage($chatId, string $messageText, array $options = null): int;

    /**
     * @param $userId
     * @param string $messageText
     * @param array|null $options
     * @return int
     */
    public function sendMessageUser($userId, string $messageText, array $options = null): int;

    /**
     * @param $data
     * @return mixed
     */
    public function update($data);

    /**
     * @return void
     */
    public function register();

    /**
     * @return void
     */
    public function unregister();

    /**
     * @param BotInterface $bot
     * @return void
     */
    public function setBot(BotInterface $bot);

    /**
     * @param UserServiceInterface $userService
     * @return void
     */
    public function setUserService(UserServiceInterface $userService);
}