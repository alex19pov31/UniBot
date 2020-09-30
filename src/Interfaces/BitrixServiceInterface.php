<?php


namespace UniBot\Interfaces;


interface BitrixServiceInterface
{
    /**
     * @return BitrixBotInterface
     */
    public static function getBitrixBot();

    /**
     * @return BitrixOptionInterface
     */
    public static function getBitrixOption();

    /**
     * @return BitrixLoaderInterface
     */
    public static function getLoader();

    /**
     * @return BitrixCMMessageInterface
     */
    public static function getCMMessage();
}