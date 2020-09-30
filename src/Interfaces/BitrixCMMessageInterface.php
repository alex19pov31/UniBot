<?php


namespace UniBot\Interfaces;


interface BitrixCMMessageInterface
{
    /**
     * @param $fromUserId
     * @param $toUserId
     * @return int
     */
    public static function GetChatId($fromUserId, $toUserId);
}