<?php


namespace UniBot;


use UniBot\Interfaces\BitrixBotInterface;
use UniBot\Interfaces\BitrixCMMessageInterface;
use UniBot\Interfaces\BitrixLoaderInterface;
use UniBot\Interfaces\BitrixOptionInterface;
use UniBot\Interfaces\BitrixServiceInterface;

class BitrixService implements BitrixServiceInterface
{
    /**
     * @return BitrixBotInterface
     */
    public static function getBitrixBot()
    {
        return new \Bitrix\Im\Bot();
    }

    /**
     * @return BitrixOptionInterface
     */
    public static function getBitrixOption()
    {
        return new \Bitrix\Main\Config\Option();
    }

    /**
     * @return BitrixLoaderInterface
     */
    public static function getLoader()
    {
        return new \Bitrix\Main\Loader();
    }

    /**
     * @return BitrixCMMessageInterface
     */
    public static function getCMMessage()
    {
        $loader = static::getLoader();
        $loader::includeModule('im');
        return new \CIMMessage();
    }
}