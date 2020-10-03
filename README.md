## UniBot - универсальный чат-бот

Данная реализация позволяет описать бизнес-логику бота не зависимо от каналов связи. Пример бота:

```php
use UniBot\BaseBot;
use UniBot\Interfaces\ProviderInterface;
use UniBot\Interfaces\EventInterface;
use UniBot\Events\InitChatEvent;
use UniBot\Events\MessageEvent;

class SimpleBot extends BaseBot
{
     public function update(EventInterface $event)
     {
         if ($event instanceof InitChatEvent) {
            $event->getMessage()->answer('Доброго времени суток! Это чат-бот.');
         }

         if ($event instanceof MessageEvent) {
            $message = $event->getMessage();
            if ($message->isCommand()) {
                $message->answer("Ответ на команду {$message->getMessageText()}...");
            } else {
                $message->answer('Ответ на сообщение...');
            }
         }
     }

     public function execute()
     {
         $userId = 10;
         $telegramProvider = $this->getProviderByCode('telegram');
         if ($telegramProvider instanceof ProviderInterface) {
             $telegramProvider->sendMessageUser($userId, 'Сообщение от чат-бота в telegram...');
         }

         $bitrixProvider = $this->getProviderByCode('bitrixBot');
         if ($bitrixProvider instanceof ProviderInterface) {
             $bitrixProvider->sendMessageUser($userId, 'Сообщение от чат-бота в bitrix чат...');
         }

         $vkProvider = $this->getProviderByCode('vk');
         if ($vkProvider instanceof ProviderInterface) {
             $vkProvider->sendMessageUser($userId, 'Сообщение от чат-бота в vk чат...');
         }
     }
}
```

Работая с локальной базой пользователей необходим отдельный сервис для идентификации пользователей во внешних сервисах:

```php
use UniBot\TelegramProvider;
use UniBot\BitrixChatProvider;
use UniBot\VKProvider;

class UserService implements UserServiceInterface
{
     public function resolveUserIdByProvider($chatId, ProviderInterface $provider)
     {
         // Это код для примера
         if ($provider instanceof TelegramProvider) { 
            $user = UserRepository::find(['telegram_chat_id' => $chatId]);
            return $user ? $user->getId() : 0;
         }

         if ($provider instanceof BitrixChatProvider) {
             $user = UserRepository::find(['bitrix_chat_id' => $chatId]);
             return $user ? $user->getId() : 0;
         }

         if ($provider instanceof VKProvider) {
             $user = UserRepository::find(['vk_chat_id' => $chatId]);
             return $user ? $user->getId() : 0;
         }

         return 0;
     }

     public function resolveChatIdByProvider($userId, ProviderInterface $provider)
     {
         // Это код для примера
         $user = UserRepository::getById($userId);
         if (!$user) {
            return 0;
         }

         if ($provider instanceof TelegramProvider) {
               return $user->getTelegramChatId();
         }

         if ($provider instanceof BitrixChatProvider) {
             return $user->getBitrixChatId();
         }

         if ($provider instanceof VKProvider) {
             return $user->getVkChatId();
         }

         return 0;
     }
}
```

Регистрация провайдеров для различных каналов связи:

```php
use UniBot\BitrixService;
use UniBot\BitrixChatProvider;
use UniBot\TelegramProvider;
use UniBot\VKProvider;

$userService = new UserService();
$bxService = new BitrixService();

$bitrixChatProvider = new BitrixChatProvider($userService, $bxService, 'bitrixBot');
$bitrixChatProvider->register();

$telegramProvider = new TelegramProvider($userService, 'my_token', 'https://some-url.com/webhook/');
$telegramProvider->register();

$vkProvider = new VKProvider($userService, 'confirm_token', 'success_token');

$data = json_decode(file_get_contents('php://input'), true);
$telegramProvider->update($data); // Получаем сообщения из чата telegram
$vkProvider->update($data); // Получаем сообщения из чата VK
```

Инициализация бота:

```php
$simpleBot = new SimpleBot();
$simpleBot->addProvider('bitrixBot', $bitrixChatProvider); // Подключаем провайдер для bitrix чата
$simpleBot->addProvider('telegram', $telegramProvider); // Подключаем провайдер для telegram
$simpleBot->addProvider('vk', $vkProvider); // Подключаем провайдер для vk чата
```