<?php


namespace UniBot;


use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\MessageInterface;
use UniBot\Interfaces\ProviderInterface;

/**
 * ```php
 * use UniBot\BaseBot;
 * use UniBot\BitrixService;
 * use UniBot\BitrixChatProvider;
 * use UniBot\TelegramProvider;
 * use UniBot\Interfaces\ProviderInterface;
 *
 * class SimpleBot extends BaseBot
 * {
 *      public function update(MessageInterface $message)
 *      {
 *          $message->answer('Доброго времени суток! Это чат-бот.');
 *      }
 *
 *      public function execute()
 *      {
 *          $userId = 10;
 *          $telegramProvider = $this->getProviderByCode('telegram');
 *          if ($telegramProvider instanceof ProviderInterface) {
 *              $telegramProvider->sendMessageUser($userId, 'Сообщение от чат-бота в telegram...');
 *          }
 *
 *          $bitrixProvider = $this->getProviderByCode('bxSuperBot');
 *          if ($telegramProvider instanceof ProviderInterface) {
 *              $bitrixProvider->sendMessageUser($userId, 'Сообщение от чат-бота в bitrix чат...');
 *          }
 *      }
 * }
 *
 * $bxService = new BitrixService()
 * $bitrixChatProvider = new BitrixChatProvider($bxService, 'superBot');
 * $bitrixChatProvider->register();
 *
 * $telegramProvider = new TelegramProvider('my_token', 'https://some-url.com/webhook/');
 * $telegramProvider->register();
 *
 * $simpleBot = new SimpleBot();
 * $simpleBot->addProvider('bxSuperBot', $bitrixChatProvider);
 * $simpleBot->addProvider('telegram', $telegramProvider);
 * ```
 *
 * Class BaseBot
 * @package UniBot
 */
abstract class BaseBot implements BotInterface
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @param MessageInterface $message
     * @return void
     */
    abstract public function update(MessageInterface $message);

    /**
     * @return void
     */
    abstract public function execute();

    /**
     * @param string $code
     * @param ProviderInterface $provider
     * @return mixed|void
     */
    public function addProvider(string $code, ProviderInterface $provider)
    {
        $provider->setBot($this);
        $this->providers[$code] = $provider;
    }

    /**
     * @param string $code
     * @return ProviderInterface|null
     */
    public function getProviderByCode(string $code)
    {
        if ($this->providers[$code] instanceof ProviderInterface) {
            return $this->providers[$code];
        }

        return null;
    }
}