<?php


namespace UniBot\Interfaces;


interface UserServiceInterface
{
    /**
     * @param $chatId
     * @param ProviderInterface $provider
     * @return mixed
     */
    public function resolveUserIdByProvider($chatId, ProviderInterface $provider);

    /**
     * @param $userId
     * @param ProviderInterface $provider
     * @return mixed
     */
    public function resolveChatIdByProvider($userId, ProviderInterface $provider);
}