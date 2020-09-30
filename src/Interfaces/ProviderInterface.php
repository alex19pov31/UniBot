<?php


namespace UniBot\Interfaces;


interface ProviderInterface
{
    public function sendMessage($chatId, string $messageText, array $options = null): int;
    public function sendMessageUser($userId, string $messageText, array $options = null): int;
    public function update($data);
    public function register();
    public function unregister();
    public function setBot(BotInterface $bot);
}