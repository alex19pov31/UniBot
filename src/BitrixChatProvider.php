<?php


namespace UniBot;


use UniBot\Events\DeleteChatEvent;
use UniBot\Events\InitChatEvent;
use UniBot\Events\MessageEvent;
use UniBot\Interfaces\BitrixBotInterface;
use UniBot\Interfaces\BitrixServiceInterface;
use UniBot\Interfaces\BotInterface;
use UniBot\Interfaces\EventInterface;
use UniBot\Interfaces\MessageInterface;
use UniBot\Interfaces\ProviderInterface;
use UniBot\Interfaces\UserServiceInterface;

class BitrixChatProvider extends BaseProvider
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
     * BitrixChatProvider constructor.
     * @param UserServiceInterface $userService
     * @param BitrixServiceInterface $bxService
     * @param string $botCode
     * @param array $options
     */
    public function __construct(UserServiceInterface $userService, $bxService, string $botCode, array $options = [])
    {
        parent::__construct($userService);
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

    public function updateBot(EventInterface $event)
    {
        if ($this->bot instanceof BotInterface) {
            $this->bot->update($event);
        }
    }

    public static function onMessageAdd($messageId, $messageFields)
    {
        $botId = $messageFields['BOT_ID'];
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }

        $message = new BitrixChatMessage($provider, [
            'message' => $messageFields['MESSAGE'],
            'chat_id' => $messageFields['DIALOG_ID'],
            'user_id' => $messageFields['FROM_USER_ID'],
            'message_id' => $messageId,
        ]);

        $event = new MessageEvent($messageFields, $messageFields['DIALOG_ID'], $message);
        $provider->updateBot($event);
    }

    public static function onChatStart($dialogId, $joinFields)
    {
        $botId = $joinFields['BOT_ID'];
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }

        $message = new BitrixChatMessage($provider, [
            'chat_id' => $dialogId,
            'user_id' => $joinFields['FROM_USER_ID'],
        ]);

        $event = new InitChatEvent($joinFields, $dialogId, $message);
        $provider->updateBot($event);
    }

    public static function onBotDelete($botId)
    {
        $provider = static::getByCode($botId);
        if (empty($provider)) {
            return;
        }

        $message = new BitrixChatMessage($provider, []);
        $event = new DeleteChatEvent([], 0, $message);
        $provider->updateBot($event);
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
        $message = new BitrixChatMessage($this, [
            'message' => $data['MESSAGE'],
            'chat_id' => $data['DIALOG_ID'],
            'user_id' => $data['FROM_USER_ID'],
            'message_id' => $data['MESSAGE_ID'],
        ]);

        switch ($data['type']) {
            case 'init':
                $event = new InitChatEvent($data, $data['DIALOG_ID'], $message);
                break;
            case 'delete':
                $event = new DeleteChatEvent($data, $data['DIALOG_ID'], $message);
                break;
            default:
                $event = new MessageEvent($data, $data['DIALOG_ID'], $message);
        }

        $this->updateBot($event);
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
}