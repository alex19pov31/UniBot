<?php


namespace UniBot;


use UniBot\Interfaces\BitrixBotInterface;
use UniBot\Interfaces\BitrixServiceInterface;
use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\MessageInterface;
use UniBot\Interfaces\ProviderInterface;

class BitrixChatProvider implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private static $instanceList;

    /**
     * @var BitrixBotInterface
     */
    private $bxBot;
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $moduleId;
    /**
     * @var mixed|string
     */
    private $lang;
    /**
     * @var mixed|string
     */
    private $name;
    /**
     * @var mixed|string
     */
    private $color;
    /**
     * @var mixed|string
     */
    private $type;
    /**
     * @var mixed|string
     */
    private $workPosition;
    /**
     * @var mixed|string
     */
    private $gender;
    /**
     * @var BitrixServiceInterface
     */
    private $bxService;
    /**
     * @var BotInterface
     */
    private $bot;

    /**
     * BitrixChatProvider constructor.
     * @param BitrixServiceInterface $bxService
     * @param string $botCode
     * @param array $options
     */
    public function __construct($bxService, string $botCode, array $options = [])
    {
        $this->bxService = $bxService;
        $this->bxBot = $bxService::getBitrixBot();
        $this->code = $botCode;
        $this->moduleId = $options['module_id'] ?? 'imbot';
        $this->lang = $options['lang'] ?? 'ru';
        $this->type = $options['type'] ?? 'H';
        $this->name = $options['name'] ?? $botCode;
        $this->color = $options['color'] ?? 'AQUA';
        $this->workPosition = $options['work_position'] ?? 'Чатбот';
        $this->gender = $options['gender'] ?? 'M';
    }

    /**
     * @param $botId
     * @return $this|null
     */
    public static function getByCode($botId)
    {
        if (static::$instanceList[$botId] instanceof ProviderInterface) {
            return static::$instanceList[$botId];
        }

        return null;
    }

    public function updateBot(MessageInterface $message)
    {
        if ($this->bot instanceof BotInterface) {
            $this->bot->update($message);
        }
    }

    public static function onMessageAdd($messageId, $messageFields)
    {
        $botId = $messageFields['BOT_ID'];
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }

        $provider->updateBot(new BitrixChatMessage($provider, [
            'message' => $messageFields['MESSAGE'],
            'chat_id' => $messageFields['DIALOG_ID'],
            'user_id' => $messageFields['FROM_USER_ID'],
            'message_id' => $messageId,
        ]));
    }

    public static function onChatStart($dialogId, $joinFields)
    {
        $botId = $joinFields['BOT_ID'];
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }


    }

    public static function onBotDelete($botId)
    {
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }
    }

    public function sendMessage($chatId, string $messageText, array $options = null): int
    {
        return $this->bxBot::addMessage(['BOT_ID' => $this->getBotId()], [
            'DIALOG_ID' => $chatId,
            'MESSAGE' => $messageText,
            'ATTACH' => $options['ATTACH'] ?? [],
            'KEYBOARD' => $options['KEYBOARD'] ?? [],
            'PARAMS' => $options['PARAMS'] ?? [],
            'URL_PREVIEW' => $options['RICH'] ?? 'Y',
        ]);
    }

    public function update($data)
    {
        // TODO: Implement update() method.
    }

    protected function getBotId(): int
    {
        $optionManager = $this->bxService::getBitrixOption();
        return $optionManager::get(
            $this->moduleId,
            "{$this->code}_bot_id",
            0
        );
    }

    public function register()
    {
        $botId = $this->getBotId();
        if ($botId > 0) {
            static::$instanceList[$botId] = $this;
            return;
        }

        $loader = $this->bxService::getLoader();
        $loader::includeModule('im');
        $loader::includeModule('imbot');
        $botId = (int)$this->bxBot::register([
            'CODE' => $this->code,
            'TYPE' => $this->type,
            'MODULE_ID' => $this->moduleId,
            'CLASS' => __CLASS__,
            'LANG' => $this->lang,
            'OPENLINE' => 'Y',
            'INSTALL_TYPE' => 'silent',
            'METHOD_MESSAGE_ADD' => 'onMessageAdd',
            'METHOD_WELCOME_MESSAGE' => 'onChatStart',
            'METHOD_BOT_DELETE' => 'onBotDelete',
            'PROPERTIES' => Array(
                'NAME' => $this->name,
                'COLOR' => $this->color,
                'WORK_POSITION' => $this->workPosition,
                'PERSONAL_GENDER' => $this->gender,
            )
        ]);

        if ($botId > 0) {
            static::$instanceList[$botId] = $this;
        }
    }

    public function unregister()
    {
        $loader = $this->bxService::getLoader();
        $loader::includeModule('im');
        $loader::includeModule('imbot');

        $this->bxBot::unRegister([
            'BOT_ID' => $this->getBotId()
        ]);
    }

    public function sendMessageUser($userId, string $messageText, array $options = null): int
    {
        $cmMessage = $this->bxService::getCMMessage();
        $chatId = $cmMessage::GetChatId($this->getBotId(), $userId);
        return $this->sendMessage($chatId, $messageText, $options);
    }

    public function setBot(BotInterface $bot)
    {
        $this->bot = $bot;
    }
}